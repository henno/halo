<?php
/**
 * Created by PhpStorm.
 * User: hennotaht
 * Date: 7/29/13
 * Time: 21:48
 */

class logout extends Controller {
	function index(){
		session_destroy();
		header('Location: '.BASE_URL);
	}
} 