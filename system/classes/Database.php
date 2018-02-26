<?php namespace App;

use SqlFormatter;

class DB
{

    private $PDO = null;
    public $debug = [];

    function __construct()
    {
        $db = DATABASE_DATABASE;
        $host = DATABASE_HOSTNAME;
        $user = DATABASE_USERNAME;
        $pass = DATABASE_PASSWORD;
        $charset = 'utf8';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->PDO = new \PDO($dsn, $user, $pass, $opt);
        } catch (\PDOException $e) {
            $errors[] = 'Database connection failed: ' . $e->getMessage();
            require 'templates/error_template.php';
            exit();
        }
    }

    /**
     * @param $sql
     * @param String[] $bindings
     * @return \PDOStatement
     */
    function query($sql, Array $bindings = [], $single_value = false)
    {

        // Remove whitespace around SQL
        $sql = trim($sql);

        $sql = $this->evaluate_bindings($sql, $bindings);

        $formatted_sql = SqlFormatter::format($sql);
        $this->debug['unformatted_sql'] = $sql;
        $this->debug['last_sql'] = $formatted_sql;
        if (defined('DB_DEBUG')) {
            $this->debug['sql'][] = $formatted_sql;
        }

        // Record query location
        $this->debug['query_location'] = $this->get_call_location();
        $this->debug['result'] = null;

        // Return a result set as a PDOStatement object
        $PDOStatement = $this->PDO->query($sql);

        switch (substr($sql, 0, 7)) {
            case 'SELECT ':
            case 'DESCRI ':
                $result = $this->build_result($PDOStatement, $single_value);
                $this->debug['result_caption'] = 'Result';
                break;
            case 'INSERT ':
                $result = $this->PDO->lastInsertId();
                $this->debug['result_caption'] = 'Inserted ID';
                break;
            case 'UPDATE ':
                $result = $PDOStatement->rowCount();
                $this->debug['result_caption'] = 'Number of affected rows';
                break;
            default:
                $result = $PDOStatement;
        }

        $this->debug['result'] = $result;

        return $result;


    }


    function insert($table, $data, $on_duplicate_key_update = false)
    {
        $data = $this->escape_array($data);
        $table = $this->escape_value($table);
        $fields = implode(',', array_keys($data));
        $values = implode(",", array_values($data));

        $sql = "INSERT INTO $table ($fields) VALUES ($values)";


        // ON DUPLICATE KEY UPDATE
        if (!empty($on_duplicate_key_update)) {
            foreach ($data as $field => $value) {
                $field_value_pairs[] = "$field=$value";

            }

            // Serialize field=value pairs with a comma
            $field_value_pairs = implode(',', $field_value_pairs);

            $sql .= " ON DUPLICATE KEY UPDATE $field_value_pairs";

        }

        return $this->query($sql);

    }

    function update($table, Array $data, $where, $where_bindings = [])
    {
        if (!is_array($data) || empty($data)) {
            return false;
        }

        $field_value_pairs = [];
        $data = $this->escape_array($data);
        $table = $this->escape_value($table);
        $fields = implode(',', array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";

        foreach ($data as $field => $value) {
            $field_value_pairs[] = "$field=$value";

        }

        // Serialize field=value pairs with a comma
        $field_value_pairs = implode(',', $field_value_pairs);

        // Create SQL
        $sql = "UPDATE $table SET $field_value_pairs";

        // Add WHERE, if needed
        empty($where) ?: $sql .= " WHERE $where";

        return $this->query($sql, $where_bindings);

    }

    private function build_result(\PDOStatement $PDOStatement, $single_value = false)
    {

        $result_rows = [];
        while ($row_from_db = $PDOStatement->fetch()) {

            $result_row = [];

            foreach ($row_from_db as $field_usv => $value) {

                $fields = explode('__', $field_usv);

                $pointer = &$result_row;

                // Build path (keys) to value
                for ($i = 0; $i < sizeof($fields); $i++) {


                    // Dynamic key names
                    if (substr($fields[$i], 0, 1) == '_') {

                        $field_name = $row_from_db[ltrim($fields[$i], '_')];

                    } else {

                        $field_name = $fields[$i];

                    }

                    // Create empty array
                    if (!isset($pointer[$field_name]) && sizeof($fields) - 1 > $i) {

                        $pointer[$field_name] = array();


                    }

                    // Point $results to sub-array
                    $pointer =& $pointer[$field_name];


                } // for

                // Assign value
                $pointer = $value;

                if ($single_value) {

                    // Return the value of the first element
                    return reset($result_row);

                }


            }
            $result_rows[] = $result_row;
        }
        return $result_rows;
    }

    function escape_array(array $data)
    {
        $values = [];
        if (!empty($data)) {
            foreach ($data as $field => $value) {
                if ($value === null) {
                    $values["`$field`"] = "NULL";
                } elseif (is_array($value) && isset($value['no_escape'])) {
                    $values["`$field`"] = "'$value[no_escape]'";
                } else {
                    $values["`$field`"] = "'" . addslashes($value) . "'";
                }
            }
        }
        return $values;
    }

    function escape_value(string $data)
    {
        return '`' . addslashes($data) . '`';
    }

    function get_call_location()
    {
        $root_dir = dirname(dirname(__DIR__));
        $backtrace = debug_backtrace();
        while (!empty($backtrace)) {

            // Move the topmost backtrace array member to $current_backtrace_item
            $current_backtrace_item = array_shift($backtrace);

            // Get current file name
            $file_name = pathinfo($current_backtrace_item['file'], PATHINFO_FILENAME);

            // Break out of while when current file is not functions.php nor Database.php
            if (!in_array($file_name, ['Database', 'functions'])) {
                break;
            }
        }

        return trim(str_replace($root_dir, '', $current_backtrace_item['file']),
                '/') . ':' . $current_backtrace_item['line'];

    }

    function render_error()
    {
        // Undo previous output
        ob_clean();

        require 'templates/db_error_template.php';
        exit();
    }

    function evaluate_bindings($sql, $bindings)
    {

        // Escape and inject bindings into SQL
        foreach ($bindings as $binding) {

            // Handle NULL
            if($binding === null){
                $binding = 'NULL';
            }

            // Add quotes to string values
            else if (!is_numeric($binding)) {
                $binding = "'$binding'";
            }

            // Protect from SQLi
            $binding = addslashes($binding);



            // Replace placeholders with values
            $sql = preg_replace('/\?/', $binding, $sql, 1);

        }

        return $sql;
    }

}


