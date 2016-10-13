<?php namespace Halo;

// Init composer auto-loading
if (!@include_once("vendor/autoload.php")) {

    exit('Run composer install');

}

// Project constants
define('PROJECT_NAME', 'halo');
define('PROJECT_NATIVE_LANGUAGE', 'en');
define('DEFAULT_CONTROLLER', 'welcome');
define('DEBUG', false);

// Load app
require 'system/classes/Application.php';
$app = new Application;
