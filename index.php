<?php namespace App;

try {

    require 'system/functions.php';
    require 'constants.php';

    convertWarningsToExceptions();

    // Init composer auto-loading
    if (!@include_once("vendor/autoload.php")) {

        exit('Run composer install');

    }

    date_default_timezone_set(DEFAULT_TIMEZONE);

    // Load config
    if (!include('config.php')) {
        $errors[] = 'No config.php. Please make a copy of config.sample.php and name it config.php and configure it.';
        require 'templates/error_template.php';
        exit();
    }

    // Default env is development
    if (!defined('ENV')) define('ENV', ENV_DEVELOPMENT);


    // Load sentry
    require 'templates/partials/sentry.php';

    new Application;

} catch (\Exception $e) {

    // To see the error message in dev
    if (ENV == ENV_PRODUCTION) {
        handleProductionError($e);
        exit();
    }

    throw $e;
}
