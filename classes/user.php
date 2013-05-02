<?php

class user {
	public $logged_in = FALSE;
	function __construct(){ // käivitub kui luuakse uus user
		session_start(); // alustab sessiooni (server hoiab $_SESSION massiivis alles kasutaja info)
		if (isset($_SESSION['user_id'])){
			$this->logged_in = TRUE;
		}
	}

	/**
	 * Kontrollib, kas kasutaja on sisselogitud, kui ei ole, siis suunab auth lehele.
	 */
	public function require_auth(){
		// annab ligipääsu request objektile
		global $request;
		if ($this->logged_in !== TRUE){
			// kontrollib kas päring tuli ajaxiga(javascript pärib) [või otse(käsitsi trükitud)]
			if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
				// vastuseks brauserile lisatakse error code(mida javascriot kontrollib)
				header('HTTP/1.0 401 Unauthorized');
				exit (json_encode(array('data'=>'session_expired')));
			} else {
				$_SESSION['session_expired'] = TRUE;
				$request->redirect('auth');
		}
		}
	}
}
$_user = new user;