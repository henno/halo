<?php namespace Halo;

use Halo;

/**
 * Created by PhpStorm.
 * User: henno
 * Date: 9/16/13
 * Time: 11:17 PM
 */
class Application
{
    public $auth = null;
    public $params = null;
    public $action = 'index';
    public $controller = DEFAULT_CONTROLLER;

    function __construct()
    {
        global $controller;
        
        ob_start();
        session_start();

        $this->load_common_functions();
        $this->set_base_url();
        $this->load_config();
        $this->set_language();
        $this->process_uri();
        $this->init_db();
        $this->handle_routing();

        $this->auth = new Auth;


        // Instantiate controller
        $controller_fqn = '\Halo\\' . $this->controller;

        if (!file_exists("controllers/$this->controller.php"))
            error_out("<b>Error:</b> File <i>controllers/{$this->controller}.php</i> does not exist.");
        require "controllers/$this->controller.php";

        if (!class_exists($controller_fqn, 1))
            error_out("<b>Error:</b>
				File  <i>controllers/{$this->controller}.php</i> exists but class <i>{$this->controller}</i> does not. You probably copied the file but forgot to rename the class in the copy.");
        $controller = new $controller_fqn();

        // Make request and auth properties available to controller
        $controller->controller = $this->controller;
        $controller->action = $this->action;
        $controller->params = $this->params;
        $controller->auth = $this->auth;

        // Check if the user has extended Controller
        if (!isset($controller->requires_auth)) {
            $errors[] = 'You forgot the "<i>extends Controller</i>" part for the class <i>' . $controller->controller . '</i> in controllers/' . $controller->controller . '.php</i>. Fix it.';
            require 'templates/error_template.php';
            exit();
        }

        // Authenticate user, if controller requires it
        if ($controller->requires_auth && !$controller->auth->logged_in) {
            $controller->auth->require_auth();
        }

        // Run the action
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && method_exists($controller, 'AJAX_'.$controller->action)) {
            $action_name = 'AJAX_'.$controller->action;
            $controller->$action_name();
            exit();
        } else {
            // Check for and process POST ( executes $action_post() )
            if (isset($_POST) && !empty($_POST) && method_exists($controller, 'POST_'.$controller->action)) {
                $action_name = 'POST_' . $controller->action;
                $controller->$action_name();
            }

            // Check for and process FILES ( executes $action_upload() )
            if (isset($_FILES) && !empty($_FILES) && method_exists($controller, 'UPLOAD_'.$controller->action)) {
                $action_name = 'UPLOAD_'.$controller->action;
                $controller->$action_name();
            }

            // Proceed with regular action processing ( executes $action() )
            if (!method_exists($controller, $controller->action))
                error_out("<b>Error:</b>
				The action <i>{$controller->controller}::{$controller->action}()</i> does not exist.
				Open <i>controllers/{$controller->controller}.php</i> and add method <i>{$controller->action}()</i>");
            $controller->{$controller->action}();
            $controller->render($controller->template);
        }

    }

    private function load_common_functions()
    {
        require dirname(__FILE__) . '/../functions.php';

    }

    private function set_base_url()
    {
        $s = & $_SERVER;
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME'];
        $uri = $protocol . '://' . $host . $port . dirname($_SERVER['SCRIPT_NAME']);
        define('BASE_URL', rtrim($uri, '/') . '/');
    }

    private function load_config()
    {
        // Load config file or bail out
        if (file_exists(dirname(__FILE__) . '/../../config.php')) {
            include dirname(__FILE__) . '/../../config.php';
        } else {
            error_out('No config.php. Please make a copy of config.sample.php and name it config.php and configure it.');
        }
    }

    private function process_uri()
    {
        if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '/') {
            $path_info = $_SERVER['PATH_INFO'];
        } elseif (!empty($_SERVER['REQUEST_URI'])) {
            // in case there's /index.php in beginning of REQUEST_URI, remove it
            // in case there's / in the beginning remove that as well
            $path_info = preg_replace(array('/\/index.php/', '/\//'), '', $_SERVER['REQUEST_URI'], 1);
        } else {
            die('horrible death - or in other words - there\'s no PATH_INFO or REQUEST_URI available');
        }

        // make sure $path_info is not empty string, otherwise controller will be set to empty string as well
        if (!empty($path_info) && $path_info = explode('/', $path_info)) {
            $this->controller = isset($path_info[0]) ? array_shift($path_info) : DEFAULT_CONTROLLER;
            $this->action = isset($path_info[0]) && !empty($path_info[0]) ? array_shift($path_info) : 'index';
            $this->params = isset($path_info[0]) ? $path_info : array();
        }
    }

    private function handle_routing()
    {
        //TODO: write here your own code if you want to manipulate controller, action

        // Allow shorter URLs (users/view/3 becomes users/3)
        if (is_numeric($this->action)) {

            // Prepend the number in action to params array
            array_unshift($this->params, $this->action);

            // Overwrite action to view
            $this->action = 'view';
        }

    }

    private function init_db()
    {
        require dirname(__FILE__) . '/../database.php';
    }

    private function set_language()
    {

        global $supported_languages;

        // Extract supported languages
        $supported_languages = array_map('trim', explode('|', WEBSITE_LANGUAGES));


        // Set default language (defaults to 'en', if no supported languages are given
        $default_language = isset($supported_languages[0]) ? $supported_languages[0] : 'en';


        // Check GET
        if (isset($_GET['language']) && in_array($_GET['language'], $supported_languages)) {

            if (is_array($_GET['language'])) {
                trigger_error('Possible hacking attempt');
            }

            $_SESSION['language'] = substr($_GET['language'], 0, 2);
            setcookie("language", $_SESSION['language'], time() + 3600 * 24 * 30);

            // Else check COOKIE
        } elseif (!empty($_COOKIE["language"]) && in_array($_COOKIE['language'], $supported_languages)) {

            $_SESSION['language'] = substr($_COOKIE['language'], 0, 2);

        } // First visit, set default langauge
        else if (!isset($_SESSION['language'])) {
            $_SESSION['language'] = $default_language;
        }

        // Else leave $_SESSION['language'] unchanged
    }


}
