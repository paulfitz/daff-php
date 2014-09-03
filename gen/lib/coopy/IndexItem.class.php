<?php

class coopy_IndexItem {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.IndexItem::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}}
	public $lst;
	public function add($i) {
		$GLOBALS['%s']->push("coopy.IndexItem::add");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->lst === null) {
			$this->lst = new _hx_array(array());
		}
		$this->lst->push($i);
		{
			$tmp = $this->lst->length;
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
	function __toString() { return 'coopy.IndexItem'; }
}
