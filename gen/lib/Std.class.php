<?php

class Std {
	public function __construct(){}
	static function string($s) {
		$GLOBALS['%s']->push("Std::string");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = _hx_string_rec($s, "");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function parseInt($x) {
		$GLOBALS['%s']->push("Std::parseInt");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!is_numeric($x)) {
			$matches = null;
			preg_match("/^-?\\d+/", $x, $matches);
			if(count($matches) === 0) {
				$GLOBALS['%s']->pop();
				return null;
			} else {
				$tmp = intval($matches[0]);
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		} else {
			if(strtolower(_hx_substr($x, 0, 2)) === "0x") {
				$tmp = (int) hexdec(substr($x, 2));
				$GLOBALS['%s']->pop();
				return $tmp;
			} else {
				$tmp = intval($x);
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Std'; }
}
