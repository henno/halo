<?php namespace Halo;

// Init composer auto-loading
die('Run composer install and remove line ' . __LINE__ . ' from index.php.');
require 'vendor/autoload.php';

// Project constants
define('PROJECT_NAME', 'halo');
define('DEFAULT_CONTROLLER', 'welcome');
define('DEBUG', false);

// Load app
require 'system/classes/Application.php';
$app = new Application;
