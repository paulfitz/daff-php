<?php

class coopy_SimpleView implements coopy_View{
	public function __construct() {}
	public function toString($d) { if(!php_Boot::$skip_constructor) {
		if($d === null) {
			return "";
		}
		return "" . Std::string($d);
	}}
	public function equals($d1, $d2) {
		if($d1 === null && $d2 === null) {
			return true;
		}
		if($d1 === null && "" . Std::string($d2) === "") {
			return true;
		}
		if("" . Std::string($d1) === "" && $d2 === null) {
			return true;
		}
		return "" . Std::string($d1) === "" . Std::string($d2);
	}
	public function toDatum($x) {
		return $x;
	}
	public function makeHash() {
		return new haxe_ds_StringMap();
	}
	public function hashSet(&$h, $str, $d) {
		$hh = $h;
		{
			$value = $d;
			$hh->set($str, $value);
		}
	}
	public function hashExists($h, $str) {
		$hh = $h;
		return $hh->exists($str);
	}
	public function hashGet($h, $str) {
		$hh = $h;
		return $hh->get($str);
	}
	public function isHash($h) {
		return Std::is($h, _hx_qtype("haxe.ds.StringMap"));
	}
	public function isTable($t) {
		return Std::is($t, _hx_qtype("coopy.Table"));
	}
	public function getTable($t) {
		return $t;
	}
	public function wrapTable($t) {
		return $t;
	}
	function __toString() { return $this->toString(); }
}
