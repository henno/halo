<?php
/**
 * Created by JetBrains PhpStorm.
 * User: karmen.kukk
 * Date: 15.04.13
 * Time: 12:48
 * To change this template use File | Settings | File Templates.
 */

class Request // objekt
{

	public $controller = DEFAULT_CONTROLLER; // muutujad, nagu array-s/ klassi sees muutuja on property
	public $action = 'index';
	public $params = array();

	public function __construct() // funktsioon saab olla ainult klassis/ väljakutsumine: ->/ klassis ees funktsioon on meetod
	{
		// kas on olemas $_SERVER-is PATH_INFO ehk kas kasutaja on kirjutanud midagi aadressirea lõppu
		// $_SERVER['PATH_INFO'] = /kasutajad/vaatamine/23
		if (isset($_SERVER['PATH_INFO'])) {
			//eraldab stringi liikmed tekitab array, kus liikmete vahel / ja paneb selle path_info-ks
			if ($path_info = explode('/', $_SERVER['PATH_INFO'])) { // explode ülemise järgi tekitab 4 liiget
				// läheb käima kui $path_info ei tagasta FALSE-i(juhul kui pole ühtegi / märki)
				array_shift($path_info); // array_shift kustutab ära esimese liikme ja reastab liikmed uuesti(uus 0)
				// $this viitab käesolevale klassile (Request)
				$this->controller = isset($path_info[0]) ? array_shift($path_info) : 'welcome';
				// array_shift võtab path_infost esimese liikme ära ja tagastab selle controllerisse
				$this->action = isset($path_info[0]) && ! empty($path_info[0]) ? array_shift($path_info) : 'index';
				$this->params = isset($path_info[0]) ? $path_info : NULL; // parameters
			}
		}
	}
	// ümbersuunamine
	public function redirect($destination){
		header('Location: '.BASE_URL.$destination); // header - aadressiribale, Location: peab olema
		// saab väärtuse kui $request->redirect(väärtus)
	}
}

$request = new Request;
