<?php

class coopy_SimpleCell {
	public function __construct($x) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.SimpleCell::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->datum = $x;
		$GLOBALS['%s']->pop();
	}}
	public $datum;
	public function toString() {
		$GLOBALS['%s']->push("coopy.SimpleCell::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->datum;
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
	function __toString() { return $this->toString(); }
}
