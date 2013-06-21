<?php

mysql_connect(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD) or db_error_out();
mysql_select_db(DATABASE_DATABASE) or db_error_out();
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER 'utf8'");

function q($sql, & $query_pointer = NULL, $debug = FALSE)
{
    if ($debug) {
        print "<pre>$sql</pre>";
    }
    $query_pointer = mysql_query($sql) or db_error_out();
    switch (substr($sql, 0, 6)) {
        case 'SELECT':
            exit("q($sql): Please don't use q() for SELECTs, use get_one() or get_first() or get_all() instead.");
        case 'INSE':
            debug_print_backtrace();
            exit("q($sql): Please don't use q() for INSERTs, use insert() instead.");
        case 'UPDA':
            exit("q($sql): Please don't use q() for UPDATEs, use update() instead.");
        default:
            return mysql_affected_rows();
    }
}

function get_one($sql, $debug = FALSE)
{
    if ($debug) { // kui debug on TRUE
        print "<pre>$sql</pre>";
    }
    switch (substr($sql, 0, 6)) {
        case 'SELECT':
            $q = mysql_query($sql) or db_error_out();
            return mysql_num_rows($q) ? mysql_result($q, 0) : null;
        default:
            exit('get_one("' . $sql . '") failed because get_one expects SELECT statement.');
    }
}

function get_all($sql)
{
    $q = mysql_query($sql) or db_error_out();
    while (($result[] = mysql_fetch_assoc($q)) || array_pop($result)) {
        ;
    }
    return $result;
}

function db_error_out($sql = NULL)
{
    $db_error = mysql_error();

    if (strpos($db_error, 'You have an error in SQL syntax') !== FALSE) {
        $db_error = '<b>Syntax error in</b><pre> ' . substr($db_error, 135) . '</pre>';

    }
    $backtrace = debug_backtrace();
    $file = $backtrace[1]['file'];
    $line = $backtrace[1]['line'];
    $function = isset($backtrace[2]['function']) ? $backtrace[2]['function'] : NULL;
    $args = isset($backtrace[2]['args']) ? $backtrace[2]['args'] : NULL;
    if (!empty($args)) {
        foreach ($args as $arg) {
            if (is_array($arg)) {
                $args2[] = implode(',', $arg);
            } else {
                $args2[] = $arg;
            }
        }
    }

    $args = empty($args2) ? '' : '"' . implode('", "', $args2) . '"';
    $s = "In file <b>$file</b>, line <b>$line</b>";
    if (!empty($function)) {
        $s .= ", function <b>$function</b>( $args )";
    }
    $output = '
            <table style="background-color:white; border:1px solid gray; border-radius:10px; padding:10px">
                <tr><td style="font-weight: bold; background-color: red; color: white; width: 100%; padding: 5px">Database error:</td></tr>
                <tr><td><pre style="text-align: left;">' . $sql . '</pre><br><b style="color: red">' . $db_error . '</b></td>
                <tr><td style="height:2px">&nbsp;</td>
                <tr><td>' . $s . '
            </table>';

    if (isset($_GET['ajax'])) {
        ob_end_clean();
        echo strip_tags($output);
    } else {
        echo $output;
    }
    die();

}

/**
 * @param $table string The name of the table to be inserted into.
 * @param $data array Array of data. For example: array('field1' => 'mystring', 'field2' => 3);
 * @return bool|int Returns the ID of the inserted row or FALSE when fails.
 */
function insert($table, $data)
{
    if ($table and is_array($data) and !empty($data)) {
        $values = null;
        foreach ($data as $field => $value) {
            $values[] = "`$field`='" . mysql_real_escape_string(trim($value)) . "'";
        }
        $values = implode(',', $values);
        $sql = "INSERT INTO `{$table}` SET {$values} ON DUPLICATE KEY UPDATE {$values}";
        $q = mysql_query($sql)or db_error_out();
        $id = mysql_insert_id();
        return ($id > 0) ? $id : FALSE;
    } else {
        return FALSE;
    }
}

function update($table, $data, $where)
{
    if ($table and is_array($data) and !empty($data)) {
        $values = null;
        foreach ($data as $field => $value) {
            $values[] = "$field='" . trim($value) . "'";
        }
        $values = implode(',', $values);
        if (isset($where)) {
            $sql = "UPDATE `{$table}` SET {$values} WHERE {$where}";
        } else {
            $sql = "UPDATE `{$table}` SET {$values}";
        }
        $id = mysql_query($sql) or db_error_out();
        return ($id > 0) ? $id : FALSE;
    } else {
        return FALSE;
    }
}