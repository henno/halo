<?php

/**
 * Display a fancy error page and quit.
 * @param $error_msg string Error message to show
 * @param int $code HTTP RESPONSE CODE. Default is 500 (Internal server error)
 * @param null $friendly_errors
 */
function error_out($error_msg = 'An error occurred', $code = 500, $friendly_errors = null)
{

    global $cfg;


    $friendly_errors = $friendly_errors === NULL ? $cfg['PRODUCTION_ENVIRONMENT'] : $friendly_errors;

    $http_error_text = substr(strip_tags(preg_replace('/\s+/', ' ', $error_msg)), 0, 100);

    if (!empty($friendly_errors)) {

        $error_msg = empty($cfg['FRIENDLY_ERROR_MESSAGE'])
            ? 'There was an error processing your request. Try again later.'
            : $cfg['FRIENDLY_ERROR_MESSAGE'];
    }

    // Return HTTP RESPONSE CODE to browser

    header(
        $_SERVER["SERVER_PROTOCOL"] . " $code $http_error_text",
        true,
        $code);


    // Show pretty error, too, to humans
    require __DIR__ . '/../templates/error_template.php';


    // Stop execution
    exit();
}

function system_error($err_str, $err_file, $err_line)
{

    global $cfg;


    $err_file = str_replace(__DIR__, '', $err_file);

    // Clear screen
    ob_clean();


    // Test if xdebug is enabled
    if (false && function_exists('xdebug_print_function_stack')) {

        xdebug_print_function_stack();
        $stack = ob_get_clean();

    } else {

        $stack = stringify_array(generateCallTrace(), 'STACK');
    }


    // Prep env data
    $ip = $_SERVER['REMOTE_ADDR'];
    $post = stringify_array($_POST, 'POST');
    $files = stringify_array($_FILES, 'FILES');
    $cookies = stringify_array($_COOKIE, 'COOKIE');
    $session = stringify_array($_SESSION, 'SESSION');
    $protocol = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
    $host = empty($_SERVER['HTTP_HOST']) ? 'example.com' : $_SERVER['HTTP_HOST'];
    $request_uri = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];
    $full_url = $protocol . $host . $request_uri;


    // Log errors
    log_error("$err_str\nURL: $full_url\nLOCATION: $err_file:$err_line\nUSER IP: $ip\n$post$session$files$cookies");


    // Send crash report to developer
    if (!empty($cfg['PRODUCTION_ENVIRONMENT']) && !empty($cfg['DEVELOPER_EMAIL']) && $cfg['DEVELOPER_EMAIL'] !== 'change.me@example.com') {

        email($cfg['DEVELOPER_EMAIL'], "[ERROR] $full_url", "$err_str\nURL: $full_url\nLOCATION: $err_file:$err_line\nUSER IP: $ip\n$post$session$files$cookies", false);

    }
    error_out("$err_str at <code id='elx' data-clipboard-target='#elx'>$err_file:$err_line</code><br><strong><br>Environment:</strong><pre>$post$session$files$cookies$stack</pre>");

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


    // Don't translate native language
    if ($active_language == PROJECT_NATIVE_LANGUAGE) {
        return $text;
    }

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
        insert('translations', ['phrase' => $text, 'translation' => '{untranslated}', 'language' => $active_language, 'controller' => $c, 'action' => $a]);


        // Set translation to input text when stub didn't exist
        $translation = $text;

    } else if ($translation == '{untranslated}') {

        // Set translation to input text when stub existed but was not yet translated
        $translation = $text;
    }

    return $translation;

}

function email($to, $subject, $message, $is_html = true)
{
    global $cfg;


    if (!class_exists('PHPMailer')) {

        require_once('vendor/phpmailer/phpmailer/PHPMailerAutoload.php');
    }

    $mail = new PHPMailer;
    $mail->SMTPDebug = $cfg['SMTP_DEBUG'];
    if (empty($cfg['SMTP_USE_SENDMAIL'])) $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = $cfg['SMTP_HOST'];  // Specify main and backup SMTP servers
    $mail->SMTPAuth = $cfg['SMTP_AUTH'];                               // Enable SMTP authentication
    $mail->Username = $cfg['SMTP_AUTH_USERNAME'];                 // SMTP username
    $mail->Password = $cfg['SMTP_AUTH_PASSWORD'];                           // SMTP password
    $mail->SMTPSecure = $cfg['SMTP_ENCRYPTION'];                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = $cfg['SMTP_PORT'] = '1025';                                    // TCP port to connect to

    $mail->setFrom($cfg['SMTP_FROM_ADDRESS'], $cfg['SMTP_FROM_NAME']);
    $mail->addAddress($to);     // Add a recipient
    $mail->isHTML($is_html);                                  // Set email format to HTML

    $mail->Subject = $subject;
    $mail->Body = $message;
    $mail->AltBody = strip_tags($message);

    if (!$mail->send()) {
        echo 'Error message was not reported to the developer because ' . $mail->ErrorInfo;
        log_error('Message could not be sent. ' . $mail->ErrorInfo);
    } else {
        echo 'Error message has been sent the developer';
    }


}

function log_error($error_msg)
{
    $error_file_pointer = fopen("error_log.txt", "a") or die("Unable to open error log file!");
    $date = date('Y-m-d H:i:s');
    $error_msg = strip_tags($error_msg);
    fwrite($error_file_pointer, "\n[$date] $error_msg\n");
    fclose($error_file_pointer);

}

function stringify_array($arr, $prefix)
{
    return empty($arr) ? '' : $prefix . ":\n" . preg_replace(
            '/Array\s+\(\s+(.*)\s*\)\s*/msiU',
            '$1',
            print_r($arr, true));

}

function generateCallTrace()
{

    $trace = debug_backtrace();
    $trace = array_reverse($trace);
    $basedir = preg_quote(dirname(__DIR__) . DIRECTORY_SEPARATOR, '/');
    //array_shift($trace); // remove {main}
    //array_pop($trace); // remove call to this method

    foreach ($trace as $call) {
        var_dump($call);


        array_map(function ($value) {
            if( is_array($value))
            {
                $value = json_encode($value, JSON_PRETTY_PRINT);
            }else{
                $value = substr($value, 0, 10);
            }
            return $value;
        }, $call['args']);

        $args = htmlentities(implode(', ', $call['args']));

        $file = preg_replace("/$basedir/i", '', $call['file']) . ':' . $call['line'];
        $func = $call['function'];
        $class = isset($call['class']) ? "$call[class]->" : '';
        var_dump("$class$func($args) at $file");
    }
    print_r($trace);


    // Remove root directory


    // reverse array to make steps line up chronologically


    $length = count($trace);
    $result = array();

    $result[] = '<table id="stack"><tr><th>#</th><th>Function/method</th><th>Called from</th></tr>';
    for ($i = 0; $i < $length; $i++) {


        $call = '<tr>';
        $call .= '<td>' . ($i + 1) . '.</td>';
        $call .= preg_replace("/.*$basedir([^(]+)\((\d+)\): (.*)/i", "<td>$3</td><td><code id='el" . $i . "' data-clipboard-target='#el" . $i . "'>$1:$2</code></td>", htmlentities($trace[$i]));
        $call .= '</tr>';
        $call .= $i == $length - 1 ? '</table>' : '';

        // Skip db_error_out() and system_error();
        if (!preg_match('/db_error_out|system_error/', $trace[$i])) {
            $result[] = $call;

        }


    }

    return "\t" . implode("\n\t", $result);
}