<?php

class coopy_IndexItem {
	public function __construct() {}
	public $lst;
	public function add($i) {
		if(!php_Boot::$skip_constructor) {
		if($this->lst === null) {
			$this->lst = new _hx_array(array());
		}
		$this->lst->push($i);
		return $this->lst->length;
	}}
	public function length() {
		return $this->lst->length;
	}
	public function value() {
		return $this->lst[0];
	}
	public function asList() {
		return $this->lst;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'coopy.IndexItem'; }
}
