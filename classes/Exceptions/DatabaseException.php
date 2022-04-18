<?php namespace App;

class DatabaseException extends CustomException
{
    public function __construct($message, $line, $file)
    {
        parent::__construct($message, $line, $file);
    }
}