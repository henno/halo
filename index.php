<?php namespace App;

// Init composer auto-loading
if (!@include_once("vendor/autoload.php")) {

    exit('Run composer install');

}
include 'system/functions.php';
include 'constants.php';

date_default_timezone_set(DEFAULT_TIMEZONE);

// Load config
if (!include('config.php')) {
    $errors[] = 'No config.php. Please make a copy of config.sample.php and name it config.php and configure it.';
    require 'templates/error_template.php';
    exit();
}


// Load app
$app = new Application;
