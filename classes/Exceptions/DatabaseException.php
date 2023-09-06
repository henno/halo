<?php namespace App;

class DatabaseException extends \Exception {
    public string $sqlQuery;
    public string $errorMessage;

    public function __construct($errorMessage, $sqlQuery) {
        parent::__construct($errorMessage);
        $this->sqlQuery = $sqlQuery;
        $this->errorMessage = $errorMessage;
    }

    public function __toString() {
        return "DatabaseException: {$this->errorMessage}, SQL Query: {$this->sqlQuery}";
    }
}
