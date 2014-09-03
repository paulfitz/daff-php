<?php

class sys_io_FileOutput extends haxe_io_Output {
	public function __construct($f) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("sys.io.FileOutput::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->__f = $f;
		$GLOBALS['%s']->pop();
	}}
	public $__f;
	public function writeByte($c) {
		$GLOBALS['%s']->push("sys.io.FileOutput::writeByte");
		$__hx__spos = $GLOBALS['%s']->length;
		$r = fwrite($this->__f, chr($c));
		if(($r === false)) {
			throw new HException(haxe_io_Error::Custom("An error occurred"));
		}
		$GLOBALS['%s']->pop();
	}
	public function writeBytes($b, $p, $l) {
		$GLOBALS['%s']->push("sys.io.FileOutput::writeBytes");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = $b->getString($p, $l);
		if(feof($this->__f)) {
			throw new HException(new haxe_io_Eof());
		}
		$r = fwrite($this->__f, $s, $l);
		if(($r === false)) {
			throw new HException(haxe_io_Error::Custom("An error occurred"));
		}
		{
			$GLOBALS['%s']->pop();
			return $r;
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
	function __toString() { return 'sys.io.FileOutput'; }
}
