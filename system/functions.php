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
    $translations_raw = get_all("SELECT controller,`action`,phrase,translation FROM translations WHERE language='$lang' AND (controller='{$controller}' and action = '{$action}') OR (action='global'  and controller = 'global')");

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
 * @param bool $global Is this a global string that should be available everywhere (for main menu, etc)
 * @return null
 */
function __($text, $global = false)
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
    $translation = isset($translations[$text]) ? $translations[$text] : null;
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

function debug($msg)
{
    if (defined(DEBUG) and DEBUG == true) {
        echo "<br>\n";
        $file = debug_backtrace()[0]['file'];
        $line = debug_backtrace()[0]['line'];
        echo "[" . $file . ":" . $line . "] <b>" . $msg . "</b>";
    }
}