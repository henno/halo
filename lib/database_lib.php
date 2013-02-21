<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dellike
 * Date: 21.02.13
 * Time: 14:58
 * To change this template use File | Settings | File Templates.
 */
mysql_connect(DATABASE_HOSTNAME, DATABASE_USERNAME, DATABASE_PASSWORD) or die(mysql_error());
mysql_select_db(DATABASE_DATABASE) or die(mysql_error());
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET utf8");

function q($sql, &$query_pointer = NULL)
{
    $query_pointer = mysql_query($sql) or die(mysql_error());
    switch (substr($sql, 0, 4)) {
        case 'SELE':
            return mysql_num_rows($query_pointer);
        case 'INSE':
            return mysql_insert_id($query_pointer);
        default:
            return mysql_affected_rows($query_pointer);
    }

}

function get_all($sql)
{
    $q = mysql_query($sql) or die(mysql_error());
    while (($result[] = mysql_fetch_assoc($q)) || array_pop($result)) {
        ;
    }
    return $result;
}

function get_one($sql)
{
    $q = mysql_query($sql) or die(mysql_error());
    if (mysql_num_rows($q) === FALSE) {
        die($sql);
    }
    $result = mysql_fetch_row($q);
    return is_array($result) && count($result) > 0 ? $result[0] : NULL;
}

function db_error_out($sql)
{
    $db_error = mysql_error();
    //kontrolli kas db_errori alguses on tekst You have an error in SQL syntax.. kui see nii on siis db_erroriks on <b> <pre
    //alates tähemärgist 135
    if (strpos($db_error, 'You have an error in SQL syntax') !== FALSE) {
        $db_error = '<b>Syntax error in</b><pre> ' . substr($db_error, 135) . '</pre>';

    }
    $backtrace = debug_backtrace();
    $file = $backtrace[1]['file'];
    $line = $backtrace[1]['line'];
    $function = isset($backtrace[2]['function']) ? $backtrace[2]['function'] : NULL;
    $args = isset($backtrace[2]['args']) ? $backtrace[2]['args'] : NULL;
}