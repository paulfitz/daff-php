<?php

class Std {
	public function __construct(){}
	static function string($s) {
		return _hx_string_rec($s, "");
	}
	static function parseInt($x) {
		if(!is_numeric($x)) {
			$matches = null;
			preg_match("/^-?\\d+/", $x, $matches);
			if(count($matches) === 0) {
				return null;
			} else {
				return intval($matches[0]);
			}
		} else {
			if(strtolower(_hx_substr($x, 0, 2)) === "0x") {
				return (int) hexdec(substr($x, 2));
			} else {
				return intval($x);
			}
		}
	}
	function __toString() { return 'Std'; }
}
