<?php

class coopy_Ordering {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.Ordering::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->order = new _hx_array(array());
		$this->ignore_parent = false;
		$GLOBALS['%s']->pop();
	}}
	public $order;
	public $ignore_parent;
	public function add($l, $r, $p = null) {
		$GLOBALS['%s']->push("coopy.Ordering::add");
		$__hx__spos = $GLOBALS['%s']->length;
		if($p === null) {
			$p = -2;
		}
		if($this->ignore_parent) {
			$p = -2;
		}
		$this->order->push(new coopy_Unit($l, $r, $p));
		$GLOBALS['%s']->pop();
	}
	public function getList() {
		$GLOBALS['%s']->push("coopy.Ordering::getList");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->order;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toString() {
		$GLOBALS['%s']->push("coopy.Ordering::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		$txt = "";
		{
			$_g1 = 0;
			$_g = $this->order->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if($i > 0) {
					$txt .= ", ";
				}
				$txt .= Std::string($this->order[$i]);
				unset($i);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $txt;
		}
		$GLOBALS['%s']->pop();
	}
	public function ignoreParent() {
		$GLOBALS['%s']->push("coopy.Ordering::ignoreParent");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ignore_parent = true;
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
