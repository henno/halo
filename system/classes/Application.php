<?php
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
		ob_start();
		session_start();

		$this->load_common_functions();
		$this->load_config();
		$this->process_uri();
		$this->handle_routing();

		$this->auth = new Auth;
		$this->init_db();


		// Instantiate controller
		require "controllers/$this->controller.php";
		$controller = new $this->controller;

		// Make request and auth properties available to controller
		$controller->controller = $this->controller;
		$controller->action = $this->action;
		$controller->params = $this->params;
		$controller->auth = $this->auth;

		// Authenticate user, if controller requires it

		if ($controller->requires_auth && !$controller->auth->logged_in) {
			$controller->auth->require_auth();
		}

		// Run the action
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $action_name = $controller->action . '_ajax';
            $controller->$action_name();
            exit();
        }else{
            // Check for and process POST ( executes $action_post() )
            if (isset($_POST) && !empty($_POST)) {
                $action_name = $controller->action . '_post';
                $controller->$action_name();
            }

            // Proceed with regular action processing ( executes $action() )
            $controller->{$controller->action}();
            $controller->render($controller->template);
        }

	}

	private function load_config()
	{
		// System paths
		define('BASE_URL', dirname($_SERVER['SCRIPT_NAME']) . '/');
		define('ASSETS_URL', BASE_URL . 'assets/');


		// Load config file or bail out
		if (file_exists('config.php')) {
			require 'config.php';
		} else {
			error_out('No config.php. Please make a copy of config.sample.php and name it config.php and configure it.');
		}
	}

	private function load_common_functions()
	{
		require 'system/functions.php';

	}

	private function process_uri()
	{
		if (isset($_SERVER['PATH_INFO'])) {
			if ($path_info = explode('/', $_SERVER['PATH_INFO'])) {
				array_shift($path_info);
				$this->controller = isset($path_info[0]) ? array_shift($path_info) : DEFAULT_CONTROLLER;
				$this->action = isset($path_info[0]) && !empty($path_info[0]) ? array_shift($path_info) : 'index';
				$this->params = isset($path_info[0]) ? $path_info : NULL;
			}
		}
	}

	private function init_db()
	{
		require 'system/database.php';
	}

	private function handle_routing()
	{
		//TODO: write here your own code if you want to manipulate controller, action
	}

}
