<?php

namespace Ykidera\Laravellib;

class HelperBuilder
{
	static private $_methods = array();
	
	public function __construct() {
		foreach (glob(app_path().'/Helpers/*.php') as $filename) {
			$name = basename($filename,'.php');
			self::$_methods[$name] = $filename;
		}
	}
	public function __call($name, array $args) {
		$filename = self::$_methods[$name];
		include_once($filename);
		return call_user_func_array('App\Helpers\\'.$name,$args);
	}
}
