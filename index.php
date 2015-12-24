<?php namespace Halo;

// Init composer auto-loading
exit('Run <i>composer install</i> and remove index.php line 4');
require 'vendor/autoload.php';

// Project constants
define('PROJECT_NAME', 'halo');
define('DEFAULT_CONTROLLER', 'welcome');
define('DEBUG', false);

// Load app
require 'system/classes/Application.php';
$app = new Application;
