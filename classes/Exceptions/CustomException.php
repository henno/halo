<?php namespace App;

use JetBrains\PhpStorm\Pure;

class CustomException extends \Exception
{
    #[Pure] public function __construct($message, $line, $file)
    {
        parent::__construct($message);
        $this->file = $file;
        $this->line = $line;
    }
}