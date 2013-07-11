<?
/**
 * Display a fancy error page and quit.
 * @param $error_file_name string The view file of the specific error (in views/errors folder, without _error_view.php suffix)
 */
function error_out($error_file_name){
	require('views/errors/master_error_view.php');
	exit();
}
