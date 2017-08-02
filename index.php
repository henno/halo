<?php

ob_start();

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


function halo_error_handler($err_no, $err_str, $err_file, $err_line, array $err_context){
    system_error($err_str, $err_file,$err_line);
}

set_error_handler("halo_error_handler", E_ALL);

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
$app = new \Halo\Application;
