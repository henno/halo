<?php

class auth
{
	public $logged_in = false;

	function __construct()
	{
		session_start();
		if (isset($_SESSION ['user_id'])) {
			$this->logged_in = true;
		}
	}
}

$auth = new auth;