<?php namespace App;

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

        $this->set_base_url();
        $this->define_current_commit_hash();

        // Redirect to HTTPS
        if (FORCE_HTTPS && $this->https_is_off()) {
            $this->redirect_to_https();
        }

        session_name(PROJECT_SESSION_ID);
        session_start();
        ob_start();

        $this->set_language();
        $this->process_uri();
        $this->update_settings();
        $this->handle_routing();
        $this->auth = new Auth();

        // Instantiate controller
        $controller_fqn = '\App\\' . $this->controller;

        if (!file_exists("controllers/$this->controller.php")) {
            error_out("<b>Error:</b> File <i>controllers/{$this->controller}.php</i> does not exist.", 404);
        }
        require "controllers/$this->controller.php";

        if (!class_exists($controller_fqn, 1)) {
            error_out("<b>Error:</b>
				File  <i>controllers/{$this->controller}.php</i> exists but class <i>{$this->controller}</i> does not. You probably copied the file but forgot to rename the class in the copy.",
                500);
        }
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

            if (!(Request::isAjax())) {
                $this->save_current_url_to_session($controller);
            }

            $controller->auth->require_auth();
        }

        // Check if user is admin, if controller requires it
        if ($controller->requires_admin && !$controller->auth->userIsAdmin && !isset($_SESSION['real_userId'])) {
            $errors[] = __('Access denied');
            require 'templates/error_template.php';
            exit();
        }

        // Run the action
        if (Request::isAjax() && method_exists($controller, 'AJAX_' . $controller->action)) {
            $action_name = 'AJAX_' . $controller->action;
            $controller->$action_name();
            stop(200);
        } else {

            // Check for and process POST ( executes $action_post() )
            if (isset($_POST) && !empty($_POST) && method_exists($controller, 'POST_' . $controller->action)) {
                $action_name = 'POST_' . $controller->action;
                $controller->$action_name();
            }

            // Check for and process FILES ( executes $action_upload() )
            if (isset($_FILES) && !empty($_FILES) && method_exists($controller, 'UPLOAD_' . $controller->action)) {
                $action_name = 'UPLOAD_' . $controller->action;
                $controller->$action_name();
            }

            // Proceed with regular action processing ( executes $action() )
            if (!method_exists($controller, $controller->action)) {
                error_out("<b>Error:</b>
				The action <i>{$controller->controller}::{$controller->action}()</i> does not exist.
				Open <i>controllers/{$controller->controller}.php</i> and add method <i>{$controller->action}()</i>",
                    404);
            }

            // Save current url, in case the action redirects to login
            $this->save_current_url_to_session($controller);

            $controller->{$controller->action}();
            $controller->render($controller->template);
        }

    }

    private function set_language()
    {

        global $supported_languages;

        // Extract supported languages
        $supported_languages = Translation::languageCodesInUse(false);


        // Set default language (defaults to 'en', if no supported languages are given
        $default_language = isset($supported_languages[0]) ? $supported_languages[0] : DEFAULT_LANGUAGE;


        // Check GET
        if (!empty($_GET['language']) && in_array($_GET['language'], $supported_languages)) {

            if (is_array($_GET['language'])) {
                trigger_error('Possible hacking attempt');
            }

            $_SESSION['language'] = substr($_GET['language'], 0, 3);
            setcookie("language", $_SESSION['language'], time() + 3600 * 24 * 30);

            // Else check COOKIE
        } elseif (!empty($_COOKIE["language"]) && in_array($_COOKIE['language'], $supported_languages)) {

            $_SESSION['language'] = substr($_COOKIE['language'], 0, 2);

        } // First visit, set default language
        else {
            if (!isset($_SESSION['language'])) {
                $_SESSION['language'] = $default_language;
            }
        }

        // If language is set to a non-existing one but there's at least one existing language, switch to that language
        if(!in_array($_SESSION['language'], $supported_languages)) {
            if(isset($supported_languages[0])){
                $_SESSION['language']=$supported_languages[0];
            }
        }

        // Else leave $_SESSION['language'] unchanged
    }

    private function set_base_url()
    {
        $s = &$_SERVER;
        $ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
        $host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME']);
        $uri = $protocol . '://' . $host . dirname($_SERVER['SCRIPT_NAME']);
        define('BASE_URL', rtrim($uri, '/') . '/');
    }


    private function process_uri()
    {


        if (isset($_SERVER['REQUEST_URI'])) {

            // Get path from REQUEST_URI
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


            // Strip directory from $path
            $project_directory = dirname($_SERVER['SCRIPT_NAME']);
            $path = substr($path, strlen($project_directory));


            // Split path parts into an array
            $path = explode('/', $path);


            // Remove empty values, due to leading or trailing or double slash, and renumber array from 0
            $path = array_values(array_filter($path));


            // Set controller, action and params
            $this->controller = isset($path[0]) ? array_shift($path) : DEFAULT_CONTROLLER;
            $this->action = isset($path[0]) && !empty($path[0]) ? array_shift($path) : 'index';
            $this->params = isset($path[0]) ? $path : array();
        } else {
            trigger_error('$_SERVER[REQUEST_URI] is undefined. Cannot continue. Make sure .htaccess is read by Apache or that NginX is configured properly.');
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

    /**
     * @param $controller
     */
    private function save_current_url_to_session($controller)
    {
        // In case the user is not logged in but this controller needs auth, redirect user back to this controller after login
        if ($controller->action != 'your_data'
            && $controller->controller != 'login_google'
            && $controller->controller != 'login'
        ) {
            $_SESSION['redirect'] = "$controller->controller/$controller->action" . ($controller->params ? '/' . $controller->params[0] : '');
        }
    }

    private function redirect_to_https()
    {

        header('Location: ' . str_replace('http://', 'https://', BASE_URL));
    }


    private function https_is_off()
    {
        return empty($_SERVER['HTTPS']);
    }

    private function define_current_commit_hash()
    {
        // Define current commit hash
        define('COMMIT_HASH', trim(exec('git log --pretty="%h" -n1 HEAD')));
    }

    private function update_settings()
    {
        $lastProjectVersion = Setting::get('projectVersion');

        // If previous version was different, analyze translations and create a new deployment
        if (COMMIT_HASH !== $lastProjectVersion) {

            $root = dirname(__DIR__, 2);

            Translation::checkCodeForNewPhrases([
                "$root/classes",
                "$root/controllers",
                "$root/system",
                "$root/templates",
                "$root/views"]);

            Deployment::create();

            Setting::set('projectVersion', COMMIT_HASH);
            Setting::set('translationUpdateLastRun', date('Y-m-d H:i:s'));

        }

    }


}
