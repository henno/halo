<?php namespace App;

use JetBrains\PhpStorm\Pure;

class CustomException extends \Exception
{
    public $file;
    public $line;

    #[Pure]
    public function __construct($message, $line = null, $file = null)
    {
        parent::__construct($message);
        if ($file !== null) {
            $this->file = $file;
        }

        if ($line !== null) {
            $this->line = $line;
        }
    }

    public function __toString() {
        return "Exception: [{$this->code}] {$this->message} in {$this->file} on line {$this->line}";
    }
}
