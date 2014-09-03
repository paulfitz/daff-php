<?php

class haxe_io_Output {
	public function __construct(){}
	public function writeByte($c) {
		$GLOBALS['%s']->push("haxe.io.Output::writeByte");
		$__hx__spos = $GLOBALS['%s']->length;
		throw new HException("Not implemented");
		$GLOBALS['%s']->pop();
	}
	public function writeBytes($s, $pos, $len) {
		$GLOBALS['%s']->push("haxe.io.Output::writeBytes");
		$__hx__spos = $GLOBALS['%s']->length;
		$k = $len;
		$b = $s->b;
		if($pos < 0 || $len < 0 || $pos + $len > $s->length) {
			throw new HException(haxe_io_Error::$OutsideBounds);
		}
		while($k > 0) {
			$this->writeByte(ord($b[$pos]));
			$pos++;
			$k--;
		}
		{
			$GLOBALS['%s']->pop();
			return $len;
		}
		$GLOBALS['%s']->pop();
	}
	public function writeFullBytes($s, $pos, $len) {
		$GLOBALS['%s']->push("haxe.io.Output::writeFullBytes");
		$__hx__spos = $GLOBALS['%s']->length;
		while($len > 0) {
			$k = $this->writeBytes($s, $pos, $len);
			$pos += $k;
			$len -= $k;
			unset($k);
		}
		$GLOBALS['%s']->pop();
	}
	public function writeString($s) {
		$GLOBALS['%s']->push("haxe.io.Output::writeString");
		$__hx__spos = $GLOBALS['%s']->length;
		$b = haxe_io_Bytes::ofString($s);
		$this->writeFullBytes($b, 0, $b->length);
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'haxe.io.Output'; }
}
