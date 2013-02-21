<?php
/**
 * Created by JetBrains PhpStorm.
 * User: hennotaht
 * Date: 2/4/13
 * Time: 21:18
 * To change this template use File | Settings | File Templates.
 */
require 'config.php';
require 'classes/request.php';

require file_exists( 'pages/' . $request->controller . '.php' ) ?
	'pages/' . $request->controller . '.php' :
	"The page '$request->controller' does not exist";