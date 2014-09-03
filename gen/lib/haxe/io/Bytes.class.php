<?php

class haxe_io_Bytes {
	public function __construct($length, $b) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("haxe.io.Bytes::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->length = $length;
		$this->b = $b;
		$GLOBALS['%s']->pop();
	}}
	public $length;
	public $b;
	public function getString($pos, $len) {
		$GLOBALS['%s']->push("haxe.io.Bytes::getString");
		$__hx__spos = $GLOBALS['%s']->length;
		if($pos < 0 || $len < 0 || $pos + $len > $this->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		{
			$tmp = substr($this->b, $pos, $len);
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
	static function ofString($s) {
		$GLOBALS['%s']->push("haxe.io.Bytes::ofString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new haxe_io_Bytes(strlen($s), $s);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'haxe.io.Bytes'; }
}
