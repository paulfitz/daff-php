<?php

class coopy_SparseSheet {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.SparseSheet::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->h = $this->w = 0;
		$GLOBALS['%s']->pop();
	}}
	public $h;
	public $w;
	public $row;
	public $zero;
	public function resize($w, $h, $zero) {
		$GLOBALS['%s']->push("coopy.SparseSheet::resize");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->row = new haxe_ds_IntMap();
		$this->nonDestructiveResize($w, $h, $zero);
		$GLOBALS['%s']->pop();
	}
	public function nonDestructiveResize($w, $h, $zero) {
		$GLOBALS['%s']->push("coopy.SparseSheet::nonDestructiveResize");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->w = $w;
		$this->h = $h;
		$this->zero = $zero;
		$GLOBALS['%s']->pop();
	}
	public function get($x, $y) {
		$GLOBALS['%s']->push("coopy.SparseSheet::get");
		$__hx__spos = $GLOBALS['%s']->length;
		$cursor = $this->row->get($y);
		if($cursor === null) {
			$tmp = $this->zero;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$val = $cursor->get($x);
		if($val === null) {
			$tmp = $this->zero;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$GLOBALS['%s']->pop();
			return $val;
		}
		$GLOBALS['%s']->pop();
	}
	public function set($x, $y, $val) {
		$GLOBALS['%s']->push("coopy.SparseSheet::set");
		$__hx__spos = $GLOBALS['%s']->length;
		$cursor = $this->row->get($y);
		if($cursor === null) {
			$cursor = new haxe_ds_IntMap();
			$this->row->set($y, $cursor);
		}
		$cursor->set($x, $val);
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
	function __toString() { return 'coopy.SparseSheet'; }
}
