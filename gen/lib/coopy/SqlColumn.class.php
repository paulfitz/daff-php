<?php

class coopy_SqlColumn {
	public function __construct() {}
	public $name;
	public $primary;
	public function getName() {
		if(!php_Boot::$skip_constructor) {
		return $this->name;
	}}
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
	static function byNameAndPrimaryKey($name, $primary) {
		$result = new coopy_SqlColumn();
		$result->name = $name;
		$result->primary = $primary;
		return $result;
	}
	function __toString() { return $this->toString(); }
}
