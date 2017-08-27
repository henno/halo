<?php namespace App;

// TODO: andmebaas nii ümber teha, et get_all konstrueeriks ise multidimensionaalse massiivi, vastavalt väljanimedele:
//       kui väljanime sees on kaks alakriipsu (foo__bar), siis tuleb tulemuses $result['foo']['bar'].
// TODO: get_all päringu tulemuses esimene number on primary key


// Init composer auto-loading
if (!@include_once("vendor/autoload.php")) {

    exit('Run composer install');

}

include __DIR__ . '/system/functions.php';

// Load config
if (file_exists('config.php')) {
    include 'config.php';
} else {
    error_out('No config.php. Please make a copy of config.sample.php and name it config.php and configure it.', 500);
}


// Project constants
define('PROJECT_NAME', 'halo');
define('PROJECT_NATIVE_LANGUAGE', 'klingong');
define('DEFAULT_CONTROLLER', 'welcome');

// Load app
require 'system/classes/Application.php';
$app = new Application;
