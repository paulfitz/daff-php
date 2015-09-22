<?php

class coopy_SqlColumn {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->name = "";
		$this->primary = false;
		$this->type_value = null;
		$this->type_family = null;
	}}
	public $name;
	public $primary;
	public $type_value;
	public $type_family;
	public function setName($name) {
		$this->name = $name;
	}
	public function setPrimaryKey($primary) {
		$this->primary = $primary;
	}
	public function setType($value, $family) {
		$this->type_value = $value;
		$this->type_family = $family;
	}
	public function getName() {
		return $this->name;
	}
	public function isPrimaryKey() {
		return $this->primary;
	}
	public function toString() {
		return _hx_string_or_null(((($this->primary) ? "*" : ""))) . _hx_string_or_null($this->name);
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
	function __toString() { return $this->toString(); }
}
