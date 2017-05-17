<?php namespace Halo;

// Init config
$cfg = [];

// Include helper function file
include __DIR__ . '/system/functions.php';

// Load config
if (file_exists('config.php')) {
    include 'config.php';
} else {
    error_out('No config.php. Please make a copy of config.sample.php and name it config.php and configure it.', 500);
}

// Set every warning to be an , so that we can catch it
set_error_handler(function ($err_no, $err_str, $err_file, $err_line, array $err_context) use ($cfg) {


    // Show error
    system_error($err_str, $err_file, $err_line);

});


// Init composer auto-loading

try {
    include_once("vendor/autoload.php");

} catch (\Exception $e) {
    error_out('Run <code>composer install</code>');
    exit();
}


// Project constants
define('PROJECT_NAME', 'halo');
define('PROJECT_NATIVE_LANGUAGE', 'en');
define('DEFAULT_CONTROLLER', 'welcome');

// Load app
require 'system/classes/Application.php';
$app = new Application;
