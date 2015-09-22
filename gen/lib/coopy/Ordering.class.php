<?php

class coopy_Ordering {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->order = new _hx_array(array());
		$this->ignore_parent = false;
	}}
	public $order;
	public $ignore_parent;
	public function add($l, $r, $p = null) {
		if($p === null) {
			$p = -2;
		}
		if($this->ignore_parent) {
			$p = -2;
		}
		$this->order->push(new coopy_Unit($l, $r, $p));
	}
	public function getList() {
		return $this->order;
	}
	public function setList($lst) {
		$this->order = $lst;
	}
	public function toString() {
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
		return $txt;
	}
	public function ignoreParent() {
		$this->ignore_parent = true;
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
