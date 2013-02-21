<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hennotaht
 * Date: 2/4/13
 * Time: 21:36
 * To change this template use File | Settings | File Templates.
 */

class request
{
	public $controller = DEFAULT_CONTROLLER;
	public $action = 'index';
	public $params = array();

	function __construct()
	{
		if (isset($_SERVER['PATH_INFO'])){
			if( $path_info = explode('/', $_SERVER['PATH_INFO']) ) {
				array_shift($path_info);
				$this->controller = isset($path_info[0]) ? array_shift($path_info) : DEFAULT_CONTROLLER;
				$this->action     = isset($path_info[0]) ? array_shift($path_info) : 'index';
				$this->params     = isset($path_info[0]) ? $path_info : null;
			}
		}
	}
}
$request = new request;