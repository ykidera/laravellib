<?

namespace Ykidera\Laravellib;

use Validator;

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