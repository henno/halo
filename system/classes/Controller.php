<?php

class Controller
{
	public $template = 'master';
	public $requires_auth = false;

	function render($template)
	{
		// Make controller variables available to view
		extract(get_object_vars($this));

		// Load view
		require 'templates/' . $template . '_template.php';
	}
} 
