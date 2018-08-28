<?php

namespace Ykidera\Laravellib;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Input;
use Validator;

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


class InputArrayObject extends \ArrayObject 
{
	function __construct($default=[],$rule=[],$message=[],$old=false){
		parent::__construct($default, self::ARRAY_AS_PROPS);
		$this->__validator = null;
		$this->__validator_default = $default;
		$this->__validator_rule    = $rule;
		$this->__validator_message = $message;
		$this->__old = $old;
	}
	function validator(){
		if(!is_object($this->__validator)){
			$this->__validator = Validator::make($this->__validator_default,$this->__validator_rule,$this->__validator_message);
		}
		return($this->__validator);
	}
	function getValidatorRule(){
		return((object)$this->__validator_rule);
	}
	function getValidatorMessage(){
		return((object)$this->__validator_message);
	}
	function old(){
		return($this->__old);
	}
	function getQuery($params = []){
		$query = [];
		foreach($this as $key => $value){
			if($key{0} != '_' && strlen($value)){
				$query[$key] = $value;
			}
		}
		if(is_array($params) && count($params)){
			$query = $params + $query;
		}
		return(http_build_query($query));
	}
	function getHidden($params = []){
		$query = [];
		foreach($this as $key => $value){
			if($key{0} != '_' && (is_array($value) || strlen($value))){
				$query[$key] = $value;
			}
		}
		if(is_array($params) && count($params)){
			$query = $params + $query;
		}
		return($this->html_build_form($query));
	}
	function html_build_form(array $array, $prefix = '') {
		$str = '';
		foreach($array as $key => $val) {
			$name = $prefix?$prefix.'['.$key.']':$key;
			if(is_array($val)){
				$str .= $this->html_build_form($val, $name);
			} else {
				$str .= '<input type="hidden" name="'.htmlentities($name).'" value="'.htmlentities($val).'">';
			}
		}
		return $str;
	}
	function all(){
		$query = [];
		foreach($this as $key => $value){
			if($key{0} != '_'){
				$query[$key] = $value;
			}
		}
		return($query);
	}
	function setPage($limit,$now=1,$max=null){
		$now = intval($now);
		$limit = intval($limit);
		if($now < 1){
			$now = 1;
		}
		$offset = ($now - 1) * $limit;
		$page = new \stdClass;
		$page->limit  = $limit;
		$page->now    = $now;
		$page->offset = $offset;
		$page->max    = $max;
		$page->last   = $page->now;
		if(!is_null($page->max)){
			$page->last = floor($page->max / $page->limit) + 1;
			if($page->now > $page->last){
				$page->now = $page->last;
			}
		}
		$page->front = $page->now - 1;
		if($page->front < 1){
			$page->front = null;
		}
		$page->next  = $page->now + 1;
		if($page->last < $page->next){
			$page->next = null;
		}
		$this->__page = $page;
	}
	function setPageMax($max){
		$this->setPage($this->__page->limit,$this->__page->now,$max);
	}
	function page(){
		return($this->__page);
	}
	function pageFront($count=1){
		if(!$this->__page->front){
			return([]);
		}
		$start = $this->__page->front;
		$end = $this->__page->front - $count + 1;
		if($end < 1){
			$end = 1;
		}
		return(range($end,$start));
	}
	function pageNext($count=1){
		if(!$this->__page->next){
			return([]);
		}
		$start = $this->__page->next;
		$end = $this->__page->next + $count - 1;
		if($end > $this->__page->last){
			$end = $this->__page->last;
		}
		return(range($start,$end));
	}
}
