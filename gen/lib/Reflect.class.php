<?php

class Reflect {
	public function __construct(){}
	static function field($o, $field) {
		$GLOBALS['%s']->push("Reflect::field");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_field($o, $field);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function fields($o) {
		$GLOBALS['%s']->push("Reflect::fields");
		$__hx__spos = $GLOBALS['%s']->length;
		if($o === null) {
			$tmp = new _hx_array(array());
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if($o instanceof _hx_array) {
			$tmp = new _hx_array(array('concat','copy','insert','iterator','length','join','pop','push','remove','reverse','shift','slice','sort','splice','toString','unshift'));
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			if(is_string($o)) {
				$tmp = new _hx_array(array('charAt','charCodeAt','indexOf','lastIndexOf','length','split','substr','toLowerCase','toString','toUpperCase'));
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$tmp = new _hx_array(_hx_get_object_vars($o));
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function isFunction($f) {
		$GLOBALS['%s']->push("Reflect::isFunction");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = (is_array($f) && is_callable($f)) || _hx_is_lambda($f) || is_array($f) && Reflect_0($f) && $f[1] !== "length";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Reflect'; }
}
function Reflect_0(&$f) {
	{
		$o = $f[0];
		$field = $f[1];
		return _hx_has_field($o, $field);
	}
}
