<?
/**
 * Display a fancy error page and quit.
 * @param $error_file_name string The view file of the specific error (in views/errors folder, without _error_view.php suffix)
 */
function error_out($error_file_name){
	require('views/errors/error_template.php');
	exit();
}

/**
 * @param $text string Text to translate
 * @return string
 */
function __($text){
//translate stuff
	return $text;
}