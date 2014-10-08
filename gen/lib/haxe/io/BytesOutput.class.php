<?php

class haxe_io_BytesOutput extends haxe_io_Output {
	public $b;
	public function writeByte($c) {
		$this->b->b .= _hx_string_or_null(chr($c));
	}
	public function writeBytes($buf, $pos, $len) {
		{
			if($pos < 0 || $len < 0 || $pos + $len > $buf->length) {
				throw new HException(haxe_io_Error::$OutsideBounds);
			}
			$this->b->b .= _hx_string_or_null(substr($buf->b, $pos, $len));
		}
		return $len;
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
	function __toString() { return 'haxe.io.BytesOutput'; }
}
