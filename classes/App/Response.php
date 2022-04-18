<?php

namespace App;

use JetBrains\PhpStorm\NoReturn;

class Response
{

    /**
     * @return bool
     */
    #[NoReturn] public static function showHtmlErrorPage($errorMessage): void
    {
        $errors[] = $errorMessage;
        require 'templates/error_template.php';
        exit();

    }

}