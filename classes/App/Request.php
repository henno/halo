<?php namespace App;

class Request
{

    /**
     * Returns true if the request is sent from JavaScript
     * @return bool
     */
    public static function isAjax(): bool
    {
        return 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
    }

    /**
     * Returns true if the script is invoked from the command line
     * @return bool
     */
    public static function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }
}