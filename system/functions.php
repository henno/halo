<?
/**
 * Display a fancy error page and quit.
 * @param $error_file_name_or_msg string The view file of the specific error (in views/errors folder, without _error_view.php suffix)
 */
function error_out($error_file_name_or_msg)
{
    if (!file_exists("views/errors/{$error_file_name_or_msg}_error_view.php")) {
        $errors[] = $error_file_name_or_msg;
    }
    require dirname(__FILE__) . '/../templates/error_template.php';
    exit();
}

function get_translation_strings($lang, $controller, $action)
{
    global $translations;
    $translations_raw = get_all("SELECT controller,`action`,phrase,translation FROM translation WHERE language='$lang' AND (controller='{$controller}' and action = '{$action}') OR (action='global'  and controller = 'global')");

    foreach ($translations_raw as $item) {
        // Uncomment this line if the same phrase need to be translated differently on different pages
        //$translations[$item['controller'] . $item['action'] . $item['phrase']] = $item['translation'];
        $translations[$item['phrase']] = $item['translation'];

    }
}

/**
 * @param $text string Text to translate
 * @return string
 */
/**
 * @param string $text Text to be translated
 * @param bool $return Should the translation be returned or echoed
 * @param bool $global Is this a global string that should be available everywhere (for main menu, etc)
 * @param string $lang Which language to translate into
 * @return null
 */
function __($text, $global = false)
{
    global $translations;
    global $controller;

    $lang = empty($_SESSION['language']) ? 'ee' : $_SESSION['language'];

    // Abnormal situation, controller should be always available, unless we aren't called from a view
    if (!isset($controller->controller)) {
        $backtrace = debug_backtrace();
        echo "<pre>";
        print_r($backtrace);
        die();
    }

    // Set translations scope
    $c = $global ? 'global' : $controller->controller;
    $a = $global ? 'global' : $controller->action;
    $page_controller = $controller->controller;
    $page_action = $controller->action;

    // Load translations only the first time (per request)
    if (empty($translations)) {
        get_translation_strings($lang, $page_controller, $page_action);
    }


    // Safe way to query translation
    $translation = isset($translations[$text]) ? $translations[$text] : null;
    // Insert new translation stub into DB when text wasn't empty but a matching translation didn't exist in the DB
    if ($text !== null && $translation == null) {

        if ($lang != 'en') {

            // Insert new stub
            $id = insert('translation', ['phrase' => $text, 'translation' => '{untranslated}', 'language' => $lang, 'controller' => $c, 'action' => $a]);


            // In case of a failure spit out debug data
            if ($id === false) {

                var_dump($text);
                var_dump($translations);
                var_dump($translations[$text]);

                echo 'There was a problem inserting missing translation stub into database';
                echo 'Obtained translation: ' . $translation;
                echo 'Tried to insert:' . $text . ' ~' . $c . '~ ' . $a . ' ~' . $lang;
                echo 'Insert ID was: ';
                var_dump($id);
                exit();
            }
        }

        // Set translation to input text when stub didn't exist
        $translation = $text;

    } else if ($translation == '{untranslated}') {

        // Set translation to input text when stub existed but was not yet translated
        $translation = $text;
    }

    return $translation;
    
}

function debug($msg)
{
    if (defined(DEBUG) and DEBUG == true) {
        echo "<br>\n";
        $file = debug_backtrace()[0]['file'];
        $line = debug_backtrace()[0]['line'];
        echo "[" . $file . ":" . $line . "] <b>" . $msg . "</b>";
    }
}