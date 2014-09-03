<?php

class coopy_CompareFlags {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.CompareFlags::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ordered = true;
		$this->show_unchanged = false;
		$this->unchanged_context = 1;
		$this->always_show_order = false;
		$this->never_show_order = true;
		$this->show_unchanged_columns = false;
		$this->unchanged_column_context = 1;
		$this->always_show_header = true;
		$this->acts = null;
		$this->ids = null;
		$GLOBALS['%s']->pop();
	}}
	public $ordered;
	public $show_unchanged;
	public $unchanged_context;
	public $always_show_order;
	public $never_show_order;
	public $show_unchanged_columns;
	public $unchanged_column_context;
	public $always_show_header;
	public $acts;
	public $ids;
	public function allowUpdate() {
		$GLOBALS['%s']->push("coopy.CompareFlags::allowUpdate");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->acts === null) {
			$GLOBALS['%s']->pop();
			return true;
		}
		{
			$tmp = $this->acts->exists("update");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function allowInsert() {
		$GLOBALS['%s']->push("coopy.CompareFlags::allowInsert");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->acts === null) {
			$GLOBALS['%s']->pop();
			return true;
		}
		{
			$tmp = $this->acts->exists("insert");
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function allowDelete() {
		$GLOBALS['%s']->push("coopy.CompareFlags::allowDelete");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->acts === null) {
			$GLOBALS['%s']->pop();
			return true;
		}
		{
			$tmp = $this->acts->exists("delete");
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
	function __toString() { return 'coopy.CompareFlags'; }
}
