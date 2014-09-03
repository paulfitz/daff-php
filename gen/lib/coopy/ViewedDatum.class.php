<?php

class coopy_ViewedDatum {
	public function __construct($datum, $view) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.ViewedDatum::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->datum = $datum;
		$this->view = $view;
		$GLOBALS['%s']->pop();
	}}
	public $datum;
	public $view;
	public function toString() {
		$GLOBALS['%s']->push("coopy.ViewedDatum::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->view->toString($this->datum);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getBag() {
		$GLOBALS['%s']->push("coopy.ViewedDatum::getBag");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->view->getBag($this->datum);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getTable() {
		$GLOBALS['%s']->push("coopy.ViewedDatum::getTable");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->view->getTable($this->datum);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function hasStructure() {
		$GLOBALS['%s']->push("coopy.ViewedDatum::hasStructure");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->view->hasStructure($this->datum);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
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
		$GLOBALS['%s']->push("coopy.ViewedDatum::getSimpleView");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new coopy_ViewedDatum($datum, new coopy_SimpleView());
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return $this->toString(); }
}
