<?php
/**
 * Database functions
 * Not included in class to shorten typing effort.
 */

use App\Backtrace;
use App\DatabaseException;
use App\Request;
use App\Response;
use Spatie\Backtrace\Backtrace as SpatieBacktrace;

connect_db();
function connect_db()
{
    global $db;

    try {
        $db = new mysqli(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_DATABASE);
    } catch (Exception $e) {
        $connection_error = mysqli_connect_error();
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);

        $errors[] = 'There was an error trying to connect to database at ' . DATABASE_HOSTNAME . ':<br><b>' . $connection_error . '</b>';
        require 'templates/error_template.php';
        die();
    }


    // Switch to utf8
    if (!$db->set_charset("utf8")) {
        trigger_error(sprintf("Error loading character set utf8: %s\n", $db->error));
        exit();
    }

    // MySQL 5.7 compatibility
    //q("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
    //q("SET sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''))");


}

function q($sql, &$query_pointer = NULL, $debug = FALSE)
{
    global $db;
    if ($debug) {
        print "<pre>$sql</pre>";
    }
    $query_pointer = mysqli_query($db, $sql) or db_error_out($sql);

    return mysqli_affected_rows($db);
}

function get_one($sql, $debug = FALSE)
{
    global $db;

    $sql = trim($sql);

    if ($debug) { // kui debug on TRUE
        print "<pre>$sql</pre>";
    }
    switch (substr($sql, 0, 6)) {
        case 'SELECT':
            $q = mysqli_query($db, $sql) or db_error_out($sql);
            $result = mysqli_fetch_array($q);
            return empty($result) ? NULL : $result[0];
        default:
            exit('get_one("' . $sql . '") failed because get_one expects SELECT statement.');
    }
}

function get_all($sql)
{
    global $db;
    $q = mysqli_query($db, $sql) or db_error_out($sql);
    while (($result[] = mysqli_fetch_assoc($q)) || array_pop($result)) {
        ;
    }
    return $result;
}

function get_first($sql)
{
    global $db;
    $q = mysqli_query($db, $sql) or db_error_out($sql);
    $first_row = mysqli_fetch_assoc($q);
    return empty($first_row) ? array() : $first_row;
}

function get_col($sql)
{
    global $db;
    $result = [];

    $col = preg_replace('/^SELECT\s+(.*)\s+FROM.*/i', '$1', $sql);

    // Check that there is just a single column selected
    if(strpos($col, ',') !== false){
        db_error_out($sql,'get_col() requires that exactly one column is selected');
    }

    $q = mysqli_query($db, $sql) or db_error_out();
    while (($row = mysqli_fetch_assoc($q)) ) {
        $result[] = $row[$col];
    }
    return $result;
}

function db_error_out($sql = null)
{
    global $db;

    if (!empty($_SERVER["SERVER_PROTOCOL"])) {
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal server error", true, 500);
    }

    $PREG_DELIMITER = '/';

    $db_error = mysqli_error($db);

    if (strpos($db_error, 'You have an error in SQL syntax') !== FALSE) {
        $db_error = '<b>Syntax error in</b><pre> ' . substr($db_error, 135) . '</pre>';

    }

    $backtrace = debug_backtrace();

    $file = $backtrace[1]['file'];
    $file = str_replace(dirname(__DIR__), '', $file);

    $line = $backtrace[1]['line'];
    $function = isset($backtrace[1]['function']) ? $backtrace[1]['function'] : NULL;
    $args = isset($backtrace[1]['args']) ? $backtrace[1]['args'] : NULL;

    // Protect the next statement failing with "Malformed UTF-8 characters, possibly incorrectly encoded" error when $args contains binary
    array_walk_recursive($args, function (&$item) {

        // Truncate item to 1000 bytes if it is longer
        if (strlen($item) > 1000) $item = mb_substr($item, 0, 1000);


        $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');

        $item = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '.', $item);
    });

    if (!empty($args)) {
        foreach ($args as $arg) {
            if (is_array($arg)) {
                $args2[] = implode(',', $arg);
            } else {
                $args2[] = $arg;
            }
        }
    }

    $args = empty($args2) ? '' : addslashes('"' . implode('", "', $args2) . '"');

    // Fault highlight
    preg_match(
        "/check the manual that corresponds to your MySQL server version for the right syntax to use near '([^']+)'./",
        $db_error,
        $output_array);

    if (!empty($output_array[1])) {
        $fault = $output_array[1];
        $fault_quoted = preg_quote($fault);


        $args = preg_replace($PREG_DELIMITER . "(\w*\s*)$fault_quoted" . $PREG_DELIMITER, "<span class='fault'>\\1$fault</span>", $args);

        $args = stripslashes($args);
    }

    $location = "<b>$file</b>:<b>$line</b> ";
    if (!empty($function)) {

        $args = str_replace('\\"', '"', $args);
        $args = str_replace("\n", '<br>', $args);
        $args = str_replace("\t", '&nbsp;', $args);

        $code = "$function(<span style=\" font-family: monospace; ;padding:0; margin:0\">$args</span>)";
        $location .= "<span class=\"line-number-position\">&#x200b;<span class=\"line-number\">$code";
    }

    // Generate stack trace
    $trace = Backtrace::reformat(SpatieBacktrace::create()
        ->applicationPath(dirname(__DIR__, 2))
        ->withArguments()
        ->frames());

    $plainTextError = "Database error:\n$db_error\n$sql\nTrace:\n # " . implode("\n # ", $trace);

    if (ENV == ENV_PRODUCTION) {
        handleProductionError(new DatabaseException($plainTextError, $line, $file));
        exit();
    }

    if (Request::isCli() || Request::isAjax()) {
        exit($plainTextError);
    } else {
        $sqlInHtml = SqlFormatter::format($sql);
        $htmlError = '<h1>Database error</h1>' .
            '<p>' . $db_error . '</p>' .
            '<p><h3>Location</h3> ' . $location . '<br>' .
            ($sqlInHtml ? '<p><h3>SQL</h3> ' . $sqlInHtml . '<br>' : '') .
            '<p><h3>Stack trace</h3>' . implode("<br>", $trace) . '</p>';

        Response::showHtmlErrorPage($htmlError);
    }


}

/**
 * @param $table string The name of the table to be inserted into.
 * @param $data array Array of data. For example: array('field1' => 'mystring', 'field2' => 3);
 * @return bool|int Returns the ID of the inserted row or FALSE when fails.
 * @throws Exception when parameters are invalid
 */
function insert($table, $data): bool|int
{
    global $db;
    if (!$table || !is_array($data) || empty($data)) {
        throw new Exception("Invalid parameter(s)");
    }
    $values = implode(',', escape($data));
    $sql = "INSERT INTO `{$table}` SET {$values} ON DUPLICATE KEY UPDATE {$values}";
    $q = mysqli_query($db, $sql) or db_error_out($sql);
    $id = mysqli_insert_id($db);

    return ($id > 0) ? $id : FALSE;
}

function update(string $table, array $data, string $where): int|string
{
    global $db;
    if (!$table || empty($data)) {
        throw new Exception("Invalid parameter(s)");
    }
    $values = implode(',', escape($data));

    if (isset($where)) {
        $sql = "UPDATE `{$table}` SET {$values} WHERE {$where}";
    } else {
        $sql = "UPDATE `{$table}` SET {$values}";
    }
    mysqli_query($db, $sql) or db_error_out($sql);

    return mysqli_affected_rows($db);
}

function escape(array $data): array
{
    $values = array();

    if (!empty($data)) {

        // Todo: Test what happens if someone tries to submit data which starts with !, like a password.
        foreach ($data as $field => $value) {
            if ($value !== null && str_starts_with($value, '!')) {
                $operator = '!=';
                $value = substr($value, 1);
            } else {
                $operator = '=';
            }

            if (!str_contains($field, '(') && !str_contains($field, '.')) {
                $field = "`$field`";
            }

            // NULL
            if ($value === NULL) {
                $values[] = "$field $operator NULL";

                // int
            } elseif (is_numeric($value)) {
                $values[] = "$field $operator " . addslashes($value);

                // no_escape
            } elseif (is_array($value) && isset($value['no_escape'])) {
                $values[] = "$field $operator " . addslashes($value['no_escape']);

                // IN(foo,bar)
            } elseif (preg_match('/^\s*IN\s*\(/i', $value)) {
                $values[] = "`$field` " . $value;

                // All other cases
            } else {
                $values[] = "$field $operator '" . addslashes($value) . "'";
            }
        }
    }
    return $values;
}

function delete(string $table, string $where): int|string
{
    global $db;
    if (!$table) {
        throw new Exception("Invalid parameter(s)");
    }

    if (!empty($where)) {
        $deleted_data = get_all("SELECT * FROM $table WHERE $where");
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
    } else {
        $deleted_data = get_all("SELECT * FROM $table");
        $sql = "DELETE FROM `{$table}`";
    }

    mysqli_query($db, $sql) or db_error_out($sql);


    return mysqli_affected_rows($db);
}