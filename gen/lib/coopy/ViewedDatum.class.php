<?php

class coopy_ViewedDatum {
	public function __construct($datum, $view) {
		if(!php_Boot::$skip_constructor) {
		$this->datum = $datum;
		$this->view = $view;
	}}
	public $datum;
	public $view;
	public function toString() {
		return $this->view->toString($this->datum);
	}
	public function getBag() {
		return $this->view->getBag($this->datum);
	}
	public function getTable() {
		return $this->view->getTable($this->datum);
	}
	public function hasStructure() {
		return $this->view->hasStructure($this->datum);
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
	static function getSimpleView($datum) {
		return new coopy_ViewedDatum($datum, new coopy_SimpleView());
	}
	function __toString() { return $this->toString(); }
}
