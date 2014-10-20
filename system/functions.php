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

function __autoload($className)
{
	(include dirname(__FILE__) . '/classes/' . $className . '.php') or
	(include dirname(__FILE__) . '/../../classes'  . $className . '.php') or
	(error_out("Autoload of class $className failed."));
	debug("Autoloaded " . $className);
}

/**
 * @param $text string Text to translate
 * @return string
 */
function __($text)
{
	//TODO: Write your own translation code here
	echo $text;
}

function debug($msg)
{
	if (defined(DEBUG) and DEBUG == true){
        echo "<br>\n";
        $file = debug_backtrace()[0]['file'];
        $line = debug_backtrace()[0]['line'];
        echo "[" . $file . ":" . $line . "] <b>" . $msg . "</b>";
    }
}