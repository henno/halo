<?php namespace App;

class Db {
    private static $instance = null;
    private $conn;
    public $debugLog = [];

    private function __construct($host, $user, $password, $dbname) {
        $this->conn = new \mysqli($host, $user, $password, $dbname);
        if ($this->conn->connect_error) {
            throw new \Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::$instance = new Db(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_DATABASE);
            } catch (\Exception $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    private function debugQuery($query, $params) {
        foreach ($params as $param) {
            $query = preg_replace('/\?/', "'{$this->conn->real_escape_string($param)}'", $query, 1);
        }
        $this->debugLog[] = $query;
        return $query;
    }

    private function displayError($message, $query) {
        $highlightedQuery = highlight_string($query, true);
        die("Error: $message<br><br><strong>Query:</strong><br><code>$highlightedQuery</code>");
    }

    private function executePrepared($query, $types = "", $params = []) {
        $debugQuery = $this->debugQuery($query, $params);
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            $this->displayError("Failed to prepare statement: " . $this->conn->error, $debugQuery);
        }
        if ($types && $params && !$stmt->bind_param($types, ...$params)) {
            $this->displayError("Failed to bind parameters: " . $stmt->error, $debugQuery);
        }
        if (!$stmt->execute()) {
            $this->displayError("Failed to execute query: " . $stmt->error, $debugQuery);
        }
        return $stmt->get_result();
    }

    public static function getOne($query, $types = "", $params = []) {
        try {
            return self::getInstance()->executePrepared($query, $types, $params)->fetch_array(MYSQLI_NUM)[0] ?? null;
        } catch (\Exception $e) {
            die("Error in getOne: " . $e->getMessage());
        }
    }

    public static function getCol($query, $types = "", $params = []) {
        try {
            $result = self::getInstance()->executePrepared($query, $types, $params);
            $output = [];
            while ($row = $result->fetch_array(MYSQLI_NUM)) $output[] = $row[0];
            return $output;
        } catch (\Exception $e) {
            die("Error in getCol: " . $e->getMessage());
        }
    }

    public static function getFirst($query, $types = "", $params = []) {
        try {
            return self::getInstance()->executePrepared($query, $types, $params)->fetch_assoc();
        } catch (\Exception $e) {
            die("Error in getFirst: " . $e->getMessage());
        }
    }

    public static function getAll($query, $types = "", $params = []) {
        try {
            $result = self::getInstance()->executePrepared($query, $types, $params);
            $output = [];
            while ($row = $result->fetch_assoc()) $output[] = $row;
            return $output;
        } catch (\Exception $e) {
            die("Error in getAll: " . $e->getMessage());
        }
    }

    public static function q($query, $types = "", $params = []) {
        try {
            self::getInstance()->executePrepared($query, $types, $params);
        } catch (\Exception $e) {
            die("Error in q: " . $e->getMessage());
        }
    }

    public static function insert($table, $data) {
        // Build field and placeholder lists
        $fields = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));

        // Prepare query
        $query = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";

        try {
            self::getInstance()->executePrepared($query, array_values($data));
            return self::getInstance()->conn->insert_id;  // Return last insert ID
        } catch (\Exception $e) {
            die("Error in insert: " . $e->getMessage());
        }
    }

    public static function update($table, $data, $whereClause, $whereParams = []) {
        // Building field updates
        $fields = array_keys($data);
        $fieldPlaceholders = implode(" = ?, ", $fields) . " = ?";

        // Prepare query
        $query = "UPDATE {$table} SET {$fieldPlaceholders} WHERE {$whereClause}";

        // Merging all values
        $values = array_merge(array_values($data), $whereParams);

        try {
            self::getInstance()->executePrepared($query, $values);
            return self::getInstance()->conn->affected_rows;  // Return the number of affected rows
        } catch (\Exception $e) {
            die("Error in update: " . $e->getMessage());
        }
    }

    public static function showDebugLog() {
        return self::getInstance()->debugLog;
    }
}