<?php

/**
 * Display a fancy error page and quit.
 * @param $error_msg string Error message to show
 * @param int $code HTTP RESPONSE CODE. Default is 500 (Internal server error)
 */
function error_out($error_msg, $code = 500)
{

    // Return HTTP RESPONSE CODE to browser
    header($_SERVER["SERVER_PROTOCOL"] . " $code Something went wrong", true, $code);


    // Set error message
    $errors[] = $error_msg;


    // Show pretty error, too, to humans
    require __DIR__ . '/../templates/error_template.php';


    // Stop execution
    exit();
}

/**
 * Loads given controller/action, and global, translations to memory
 * @param $lang string Language to load
 * @param $controller string Controller to load translations for
 * @param $action string Action to load translations for
 */
function get_translation_strings($lang, $controller, $action)
{
    global $translations;
    $translations_raw = get_all("SELECT controller,`action`,phrase,translation FROM translations WHERE language='$lang' AND ((controller='{$controller}' and action = '{$action}') OR (action='global'  and controller = 'global'))");

    foreach ($translations_raw as $item) {
        // Uncomment this line if the same phrase need to be translated differently on different pages
        //$translations[$item['controller'] . $item['action'] . $item['phrase']] = $item['translation'];
        $translations[$item['phrase']] = $item['translation'];

    }
}

/**
 * Translates the text into currently selected language
 * @param $text string The text to be translated
 * @param bool $global Set to false if you want to let the user translate this string differently on different sub-pages.
 * @return string Translated text
 */
function __($text, $global = true)
{
    global $translations;
    global $controller;

    $active_language = $_SESSION['language'];

    // Controller should be always available, unless we aren't called from a view
    if (!isset($controller->controller)) {
        $global = true;
    }

    // Set translations scope
    $c = $global ? 'global' : $controller->controller;
    $a = $global ? 'global' : $controller->action;
    $page_controller = $controller->controller;
    $page_action = $controller->action;

    // Load translations only the first time (per request)
    if (empty($translations) && $active_language) {
        get_translation_strings($active_language, $page_controller, $page_action);
    }


    // Safe way to query translation
    $translation = isset($translations[$text]) ? $translations[$text] : '';
    // Insert new translation stub into DB when text wasn't empty but a matching translation didn't exist in the DB
    if ($text !== null && $translation == null) {


        // Insert new stub
        insert('translations', [
            'phrase' => $text,
            'translation' => '{untranslated}',
            'language' => $active_language,
            'controller' => $c,
            'action' => $a
        ]);


        // Set translation to input text when stub didn't exist
        $translation = $text;

    } else {
        if ($translation == '{untranslated}') {

            // Set translation to input text when stub existed but was not yet translated
            $translation = $text;
        }
    }

    return $translation;

}

function stop($code, $data = false)
{
    $response['status'] = $code;

    if ($data) {
        $response['data'] = $data;
    }

    exit(json_encode($response));
}

/** Shortcut functions */


function get_first($sql, ...$bindings)
{
    $result = db_action('select', ['sql' => $sql . ' LIMIT 1', 'bindings' => $bindings, 'single_value'=> 0]);
    return empty($result[0]) ? [] : $result[0];
}

function get_one($sql, ...$bindings)
{
    $result = db_action('select', ['sql' => $sql . ' LIMIT 1', 'bindings' => $bindings, 'single_value' => true]);
    // TODO: might not work as expected; reset() is a better idea?
    return empty($result[0]) ? [] : $result[0];
}

function get_all($sql, ...$bindings)
{
    return db_action('select', ['sql' => $sql, 'bindings' => $bindings, 'single_value' => false]);
}

function insert($table, $data, $on_duplicate_key_update = false)
{
    return db_action('insert',
        ['table' => $table, 'data' => $data, 'on_duplicate_key_update' => $on_duplicate_key_update]);
}

function update($table, $data, $where, ...$where_bindings)
{
    return db_action('update', ['data' => $data, 'where' => $where, 'where_bindings' => $where_bindings]);
}

function q($sql)
{
    return db_action('query', ['sql' => $sql, 'bindings' => $bindings]);
}

function db_action($action, $args)
{
    /**  @var $db \App\DB */
    global $db;

    try {

        switch ($action) {

            case 'insert':
                $result = $db->insert($args['table'], $args['data'], $args['on_duplicate_key_update']);
                break;

            case 'update':
                $result = $db->update($args['table'], $args['data'], $args['where'], $args['where_bindings']);
                break;

            case 'select':
                $result = $db->query($args['sql'], $args['bindings'], $args['single_value']);
                break;
            default:
                $result = $db->query($args['sql'], $args['bindings'], false);
        }

    } catch (\Exception $e) {
        // Syntax error
        if($e->getCode() == '42000'){
            $db->debug['sql'] = $db->debug['unformatted_sql'];
        }
        $db->debug['error'] = $e->getMessage();
    }

    if (!empty($e) || defined('DB_DEBUG')) {

        $db->render_error();
    }

    return $result;

}


/**
 * Print SQL and the result and end the script on the next database query
 */
function debug_next_query()
{
    /**  @var $db \App\DB */
    global $db;
    $db->debug['debug_location'] = $db->get_call_location();
    define('DB_DEBUG', true);

}

function strip_root_dir($path)
{
    $root_dir = dirname(__DIR__);
    return trim(str_replace($root_dir, '', $path), '/');

}

function array_to_text($array, $separator)
{
    return urldecode(http_build_query($array, null, $separator));
}

function get_function_stack()
{
    $result = [];
    $xdebug = function_exists('xdebug_get_function_stack');

    if ($xdebug) {
        $stack = xdebug_get_function_stack();
    } else {
        $stack = debug_backtrace();
        krsort($stack);
    }

    foreach ($stack as $nr => $item) {

        if (@$item['class'] == 'App\DB'
            || $item['function'] == '{main}'
            || strpos($item['file'], 'classes/Database.php') !== false
            || strpos($item['file'], 'templates/db_error_template.php') !== false
            || strpos($item['function'], 'get_function_stack') !== false
            || strpos($item['function'], 'db_action') !== false
        ) {
            continue;
        }


        $type = @$item['type'] == 'static' ? '::' : '->';
        $function = empty($item['class']) ? "$item[function]()" : "$item[class]{$type}$item[function]()";
        $params = empty($item['params']) ? (empty($item['args']) ? [] : $item['args']) : $item['params'];

        // Prettify SQL
        prettify_sql($params);

        // Stringify params to JSON
        $params = empty($params) ? '' : pretty_print($params);


        $result[$nr] = [
            'function' => $function,
            'location' => strip_root_dir($item['file']) . ':' . $item['line'],
            'params' => $params

        ];
    }
    return $result;
}

function pretty_print($array)
{
    return stripcslashes(json_encode($array, JSON_PRETTY_PRINT));
}

function prettify_sql(&$params)
{
    foreach ($params as &$param) {

        if (!is_array($param)) {

            if (function_exists('xdebug_get_function_stack')) {

                // Remove extra quotes from strings
                $param = preg_replace("/^'(.*)'$/", "$1", $param);

                // Convert \n and \t to newline and tab
                $param = stripcslashes($param);

            }
            if (preg_match('/(\'|")?(SELECT[^\1]+)\1?/', $param, $matches)) {
                $param = SqlFormatter::format($matches[2]);
            }
        } else {
            prettify_sql($param);
        }
    }

}

function halo_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorHandler($errstr, 0, $errno, $errfile, $errline);
}

class ErrorHandler extends Exception {
    protected $severity;

    public function __construct($message, $code, $severity, $filename, $lineno) {
        $this->message = $message;
        $this->code = $code;
        $this->severity = $severity;
        $this->file = $filename;
        $this->line = $lineno;
    }

    public function getSeverity() {
        return $this->severity;
    }
}
