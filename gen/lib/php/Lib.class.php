<?php

class php_Lib {
	public function __construct(){}
	static function toPhpArray($a) {
		$GLOBALS['%s']->push("php.Lib::toPhpArray");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $a->a;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function associativeArrayOfHash($hash) {
		$GLOBALS['%s']->push("php.Lib::associativeArrayOfHash");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $hash->h;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function associativeArrayOfObject($ob) {
		$GLOBALS['%s']->push("php.Lib::associativeArrayOfObject");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = (array) $ob;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'php.Lib'; }
}
