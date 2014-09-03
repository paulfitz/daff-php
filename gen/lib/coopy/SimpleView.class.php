<?php

class coopy_SimpleView implements coopy_View{
	public function __construct() { if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.SimpleView::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}}
	public function toString($d) {
		$GLOBALS['%s']->push("coopy.SimpleView::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		if($d === null) {
			$GLOBALS['%s']->pop();
			return null;
		}
		{
			$tmp = "" . Std::string($d);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getBag($d) {
		$GLOBALS['%s']->push("coopy.SimpleView::getBag");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	public function getTable($d) {
		$GLOBALS['%s']->push("coopy.SimpleView::getTable");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return null;
		}
		$GLOBALS['%s']->pop();
	}
	public function hasStructure($d) {
		$GLOBALS['%s']->push("coopy.SimpleView::hasStructure");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return false;
		}
		$GLOBALS['%s']->pop();
	}
	public function equals($d1, $d2) {
		$GLOBALS['%s']->push("coopy.SimpleView::equals");
		$__hx__spos = $GLOBALS['%s']->length;
		if($d1 === null && $d2 === null) {
			$GLOBALS['%s']->pop();
			return true;
		}
		if($d1 === null && "" . Std::string($d2) === "") {
			$GLOBALS['%s']->pop();
			return true;
		}
		if("" . Std::string($d1) === "" && $d2 === null) {
			$GLOBALS['%s']->pop();
			return true;
		}
		{
			$tmp = "" . Std::string($d1) === "" . Std::string($d2);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toDatum($str) {
		$GLOBALS['%s']->push("coopy.SimpleView::toDatum");
		$__hx__spos = $GLOBALS['%s']->length;
		if($str === null) {
			$GLOBALS['%s']->pop();
			return null;
		}
		{
			$tmp = new coopy_SimpleCell($str);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return $this->toString(); }
}
