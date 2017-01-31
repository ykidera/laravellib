<?php

namespace Ykidera\Laravellib;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Input;
use Ykidera\Laravellib\InputArrayObject;

class InoutBuilder
{
	var $_names = null;
	
	public function input($format=[],$validator=null){
		if(!is_array($format)){
			$format = [$format];
		}
		$name = $this->getName();
		$validator_config = 'validator.'.$this->makePath($name,$validator);
		$validator = config($validator_config.'.rule');
		if(!is_array($validator)){
			$validator = [];
		}
		$message = config($validator_config.'.message');
		if(!is_array($message)){
			$message = [];
		}
		$input = Input::old();
		$old = true;
		if(!$input){
			$input = Input::all();
			$old   = false;
		}
		return(new InputArrayObject($input+$format,$validator,$message,$old));
	}
	public function view($returnData=[],$template_name=null){
		if(is_null($template_name)){
			$name = $this->getName();
			$template_name = $this->makePath($name);
		}
		return view($template_name,(array)$returnData);
	}
	public function route($action='.index',$param=[]){
		$name = $this->getName();
		$redirect = $this->makePath($name,$action);
		if(substr($redirect,-6,6) == '.index'){
			$redirect = substr($redirect,0,strlen($redirect)-6);
		}
		return route($redirect,$param);
	}
	public function redirectBack($input){
		return redirect()->back()->withErrors($input->validator())->withInput($input->all());
	}
	public function redirect($action='.index',$param=[]){
		$name = $this->getName();
		$redirect = $this->makePath($name,$action);
		if(substr($redirect,-6,6) == '.index'){
			$redirect = substr($redirect,0,strlen($redirect)-6);
		}
		return redirect()->route($redirect,$param);
	}
	public function makePath($name,$path=null){
		$full_path = $path;
		if(is_null($path) || !strlen($path)){
			$full_path = $name["name_space"].".".$name["controller_name"].".".$name["action_name"];
		} else if(substr($path,0,1) == '.'){
			$full_paths = explode('.',$name["name_space"].".".$name["controller_name"].".".$name["action_name"]);
			$paths = explode('.',$path);
			for($i = 1;$i < count($paths);$i++){
				array_pop($full_paths);
			}
			for($i = 1;$i < count($paths);$i++){
				array_push($full_paths, $paths[$i]);
			}
			$full_path = implode('.',$full_paths);
		}
		return(strtolower($full_path));
	}
	public function getName(){
		if(is_null($this->_names)){
			$arr = explode("\\",Route::currentRouteAction());
			$class_name = array_pop($arr);
			list($controller_name,$action_name) = explode("Controller@",$class_name);
			if(($key = array_search('Controllers',$arr)) !== false){
				$name_spaces = [];
				for($i = $key + 1;$i < count($arr);$i++){
					$name_spaces[] = $arr[$i];
				}
				$name_space = implode('.',$name_spaces);
			} else {
			$name_space = array_pop($arr);
			}
			
			$name_space = preg_replace('/([a-z])([A-Z]+)/','$1-$2',$name_space);
			$controller_name = preg_replace('/([a-z])([A-Z]+)/','$1-$2',$controller_name);
			$action_name = preg_replace('/([a-z])([A-Z]+)/','$1-$2',$action_name);
			$this->_names = [
				"name_space"      => $name_space,
				"controller_name" => $controller_name,
				"action_name"     => $action_name,
			];
		}
		return($this->_names);
	}
}
