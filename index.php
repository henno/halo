<?php namespace App;

// set to the user defined error handler
// Init composer auto-loading
if (!@include_once("vendor/autoload.php")) {

    exit('Run composer install');

}

include 'system/functions.php';
include 'constants.php';

set_error_handler("halo_error_handler", E_ALL);



// Load config
if (file_exists('config.php')) {
    include 'config.php';
} else {
    error_out('No config.php. Please make a copy of config.sample.php and name it config.php and configure it.', 500);
}


// Load app
try{

    $app = new Application;
} catch (\Exception $e){
    die('a');
}

