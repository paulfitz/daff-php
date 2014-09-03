<?php

class Date {
	public function __construct(){}
	public $__t;
	public function toString() {
		$GLOBALS['%s']->push("Date::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = date("Y-m-d H:i:s", $this->__t);
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
