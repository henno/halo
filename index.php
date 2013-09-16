<?php

session_start();
require 'functions.php';

// Load config or bail out
if (file_exists('config.php')) {
	require 'config.php';
}
else {
	error_out('no_config');
}

//
require 'modules/request.php';
require 'modules/database.php';
require 'modules/auth.php';

$request = new request;

if (file_exists('controllers/'.$request->controller.'.php')) {

	// Instantiate controller
	require 'controllers/'.$request->controller.'.php';
	$controller = new $request->controller;

	// Make request and auth properties available to controller
	$controller->controller = $request->controller;
	$controller->action = $request->action;
	$controller->params = $request->params;
	$controller->auth = new auth();

	// Authenticate user, if controller requires it
	if (isset($controller->requires_auth)) {

		$controller->auth->require_auth();

	}

	// Run the action
	$controller->{$controller->action}();

}
else {

	// The specified controller does not exist
	echo "The page '{$request->controller}' does not exist";
}
