<?php

class Std {
	public function __construct(){}
	static function is($v, $t) {
		return _hx_instanceof($v, $t);
	}
	static function string($s) {
		return _hx_string_rec($s, "");
	}
	static function int($x) {
		$i = fmod($x, -2147483648) & -1;
		if($i & -2147483648) {
			$i = -((~$i & -1) + 1);
		}
		return $i;
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
	static function parseFloat($x) {
		$v = floatval($x);
		if($v === 0.0) {
			$x = rtrim($x);
			$v = floatval($x);
			if($v === 0.0 && !is_numeric($x)) {
				$v = acos(1.01);
			}
		}
		return $v;
	}
	function __toString() { return 'Std'; }
}
