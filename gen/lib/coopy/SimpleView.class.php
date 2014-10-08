<?php

class coopy_SimpleView implements coopy_View{
	public function __construct() {}
	public function toString($d) { if(!php_Boot::$skip_constructor) {
		if($d === null) {
			return null;
		}
		return "" . Std::string($d);
	}}
	public function getBag($d) {
		return null;
	}
	public function getTable($d) {
		return null;
	}
	public function hasStructure($d) {
		return false;
	}
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
	public function toDatum($str) {
		if($str === null) {
			return null;
		}
		return new coopy_SimpleCell($str);
	}
	function __toString() { return $this->toString(); }
}
