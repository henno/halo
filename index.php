<?php

session_start();
require 'functions.php';

// Load config or bail out
if (file_exists('config.php')) {
	require 'config.php';
} else {
	error_out('no_config');
}

//
require 'modules/Request.php';
require 'modules/database.php';

if (file_exists('controllers/'.$request->controller.'.php')) {

	// Instantiate controller
	$file_extension = '.php';
	require 'controllers/'.$request->controller.$file_extension;
	$controller = new $request->controller;

	// Authenticate user, if controller requires it
	if (isset($controller->requires_auth)) {
		require 'modules/auth.php';
		$auth->require_auth();
	}

	// Run the action
	$controller->{$request->action}();

} else {

	// The specified controller does not exist
	echo "The page '{$request->controller}' does not exist";
}
