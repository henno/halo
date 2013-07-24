<?php

class admin {

	public $requires_auth = true;

	function index(){
		global $request;
		require 'views/master_view.php';
	}
}