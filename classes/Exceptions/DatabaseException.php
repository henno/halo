<?php namespace App;

class DatabaseException extends CustomException {
    public string $operation;
    public string $sqlQuery;
    public string $errorMessage;

    public function __construct($operation, $sqlQuery, $line, $file, $errorMessage) {
        parent::__construct($errorMessage, $line, $file);
        $this->operation = $operation;
        $this->sqlQuery = $sqlQuery;
        $this->errorMessage = $errorMessage;
    }

    public function __toString() {
        return "DatabaseException: [{$this->code}] {$this->errorMessage} in {$this->file} on line {$this->line} "
            . "Operation: {$this->operation}, SQL Query: {$this->sqlQuery}";
    }
}
