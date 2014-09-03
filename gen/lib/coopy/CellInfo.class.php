<?php

class coopy_CellInfo {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.CellInfo::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}}
	public $value;
	public $pretty_value;
	public $category;
	public $category_given_tr;
	public $separator;
	public $pretty_separator;
	public $updated;
	public $conflicted;
	public $pvalue;
	public $lvalue;
	public $rvalue;
	public function toString() {
		$GLOBALS['%s']->push("coopy.CellInfo::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!$this->updated) {
			$tmp = $this->value;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		if(!$this->conflicted) {
			$tmp = _hx_string_or_null($this->lvalue) . "::" . _hx_string_or_null($this->rvalue);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$tmp = _hx_string_or_null($this->pvalue) . "||" . _hx_string_or_null($this->lvalue) . "::" . _hx_string_or_null($this->rvalue);
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
