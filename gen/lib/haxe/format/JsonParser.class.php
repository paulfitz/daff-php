<?php

class haxe_format_JsonParser {
	public function __construct($str) {
		if(!php_Boot::$skip_constructor) {
		$this->str = $str;
		$this->pos = 0;
	}}
	public $str;
	public $pos;
	public function parseRec() {
		while(true) {
			$c = null;
			{
				$index = $this->pos++;
				$c = ord(substr($this->str,$index,1));
				unset($index);
			}
			switch($c) {
			case 32:case 13:case 10:case 9:{}break;
			case 123:{
				$obj = _hx_anonymous(array());
				$field = null;
				$comma = null;
				while(true) {
					$c1 = null;
					{
						$index1 = $this->pos++;
						$c1 = ord(substr($this->str,$index1,1));
						unset($index1);
					}
					switch($c1) {
					case 32:case 13:case 10:case 9:{}break;
					case 125:{
						if($field !== null || $comma === false) {
							$this->invalidChar();
						}
						return $obj;
					}break;
					case 58:{
						if($field === null) {
							$this->invalidChar();
						}
						{
							$value = $this->parseRec();
							$obj->{$field} = $value;
						}
						$field = null;
						$comma = true;
					}break;
					case 44:{
						if($comma) {
							$comma = false;
						} else {
							$this->invalidChar();
						}
					}break;
					case 34:{
						if($comma) {
							$this->invalidChar();
						}
						$field = $this->parseString();
					}break;
					default:{
						$this->invalidChar();
					}break;
					}
					unset($c1);
				}
			}break;
			case 91:{
				$arr = (new _hx_array(array()));
				$comma1 = null;
				while(true) {
					$c2 = null;
					{
						$index2 = $this->pos++;
						$c2 = ord(substr($this->str,$index2,1));
						unset($index2);
					}
					switch($c2) {
					case 32:case 13:case 10:case 9:{}break;
					case 93:{
						if($comma1 === false) {
							$this->invalidChar();
						}
						return $arr;
					}break;
					case 44:{
						if($comma1) {
							$comma1 = false;
						} else {
							$this->invalidChar();
						}
					}break;
					default:{
						if($comma1) {
							$this->invalidChar();
						}
						$this->pos--;
						$arr->push($this->parseRec());
						$comma1 = true;
					}break;
					}
					unset($c2);
				}
			}break;
			case 116:{
				$save = $this->pos;
				if(haxe_format_JsonParser_0($this, $c, $save) !== 114 || haxe_format_JsonParser_1($this, $c, $save) !== 117 || haxe_format_JsonParser_2($this, $c, $save) !== 101) {
					$this->pos = $save;
					$this->invalidChar();
				}
				return true;
			}break;
			case 102:{
				$save1 = $this->pos;
				if(haxe_format_JsonParser_3($this, $c, $save1) !== 97 || haxe_format_JsonParser_4($this, $c, $save1) !== 108 || haxe_format_JsonParser_5($this, $c, $save1) !== 115 || haxe_format_JsonParser_6($this, $c, $save1) !== 101) {
					$this->pos = $save1;
					$this->invalidChar();
				}
				return false;
			}break;
			case 110:{
				$save2 = $this->pos;
				if(haxe_format_JsonParser_7($this, $c, $save2) !== 117 || haxe_format_JsonParser_8($this, $c, $save2) !== 108 || haxe_format_JsonParser_9($this, $c, $save2) !== 108) {
					$this->pos = $save2;
					$this->invalidChar();
				}
				return null;
			}break;
			case 34:{
				return $this->parseString();
			}break;
			case 48:case 49:case 50:case 51:case 52:case 53:case 54:case 55:case 56:case 57:case 45:{
				$c3 = $c;
				$start = $this->pos - 1;
				$minus = $c3 === 45;
				$digit = !$minus;
				$zero = $c3 === 48;
				$point = false;
				$e = false;
				$pm = false;
				$end = false;
				while(true) {
					{
						$index13 = $this->pos++;
						$c3 = ord(substr($this->str,$index13,1));
						unset($index13);
					}
					switch($c3) {
					case 48:{
						if($zero && !$point) {
							$this->invalidNumber($start);
						}
						if($minus) {
							$minus = false;
							$zero = true;
						}
						$digit = true;
					}break;
					case 49:case 50:case 51:case 52:case 53:case 54:case 55:case 56:case 57:{
						if($zero && !$point) {
							$this->invalidNumber($start);
						}
						if($minus) {
							$minus = false;
						}
						$digit = true;
						$zero = false;
					}break;
					case 46:{
						if($minus || $point) {
							$this->invalidNumber($start);
						}
						$digit = false;
						$point = true;
					}break;
					case 101:case 69:{
						if($minus || $zero || $e) {
							$this->invalidNumber($start);
						}
						$digit = false;
						$e = true;
					}break;
					case 43:case 45:{
						if(!$e || $pm) {
							$this->invalidNumber($start);
						}
						$digit = false;
						$pm = true;
					}break;
					default:{
						if(!$digit) {
							$this->invalidNumber($start);
						}
						$this->pos--;
						$end = true;
					}break;
					}
					if($end) {
						break;
					}
				}
				$f = Std::parseFloat(_hx_substr($this->str, $start, $this->pos - $start));
				$i = Std::int($f);
				if(_hx_equal($i, $f)) {
					return $i;
				} else {
					return $f;
				}
			}break;
			default:{
				$this->invalidChar();
			}break;
			}
			unset($c);
		}
	}
	public function parseString() {
		$start = $this->pos;
		$buf = new StringBuf();
		while(true) {
			$c = null;
			{
				$index = $this->pos++;
				$c = ord(substr($this->str,$index,1));
				unset($index);
			}
			if($c === 34) {
				break;
			}
			if($c === 92) {
				$buf->b .= _hx_string_or_null(_hx_substr($this->str, $start, $this->pos - $start - 1));
				{
					$index1 = $this->pos++;
					$c = ord(substr($this->str,$index1,1));
					unset($index1);
				}
				switch($c) {
				case 114:{
					$buf->b .= "\x0D";
				}break;
				case 110:{
					$buf->b .= "\x0A";
				}break;
				case 116:{
					$buf->b .= "\x09";
				}break;
				case 98:{
					$buf->b .= "\x08";
				}break;
				case 102:{
					$buf->b .= "\x0C";
				}break;
				case 47:case 92:case 34:{
					$buf->b .= _hx_string_or_null(chr($c));
				}break;
				case 117:{
					$uc = Std::parseInt("0x" . _hx_string_or_null(_hx_substr($this->str, $this->pos, 4)));
					$this->pos += 4;
					if($uc <= 127) {
						$buf->b .= _hx_string_or_null(chr($uc));
					} else {
						if($uc <= 2047) {
							$buf->b .= _hx_string_or_null(chr(192 | $uc >> 6));
							$buf->b .= _hx_string_or_null(chr(128 | $uc & 63));
						} else {
							if($uc <= 65535) {
								$buf->b .= _hx_string_or_null(chr(224 | $uc >> 12));
								$buf->b .= _hx_string_or_null(chr(128 | $uc >> 6 & 63));
								$buf->b .= _hx_string_or_null(chr(128 | $uc & 63));
							} else {
								$buf->b .= _hx_string_or_null(chr(240 | $uc >> 18));
								$buf->b .= _hx_string_or_null(chr(128 | $uc >> 12 & 63));
								$buf->b .= _hx_string_or_null(chr(128 | $uc >> 6 & 63));
								$buf->b .= _hx_string_or_null(chr(128 | $uc & 63));
							}
						}
					}
				}break;
				default:{
					throw new HException("Invalid escape sequence \\" . _hx_string_or_null(chr($c)) . " at position " . _hx_string_rec(($this->pos - 1), ""));
				}break;
				}
				$start = $this->pos;
			} else {
				if($c >= 128) {
					$this->pos++;
					if($c >= 252) {
						$this->pos += 4;
					} else {
						if($c >= 248) {
							$this->pos += 3;
						} else {
							if($c >= 240) {
								$this->pos += 2;
							} else {
								if($c >= 224) {
									$this->pos++;
								}
							}
						}
					}
				} else {
					if(($c === 0)) {
						throw new HException("Unclosed string");
					}
				}
			}
			unset($c);
		}
		$buf->b .= _hx_string_or_null(_hx_substr($this->str, $start, $this->pos - $start - 1));
		return $buf->b;
	}
	public function invalidChar() {
		$this->pos--;
		throw new HException("Invalid char " . _hx_string_rec(ord(substr($this->str,$this->pos,1)), "") . " at position " . _hx_string_rec($this->pos, ""));
	}
	public function invalidNumber($start) {
		throw new HException("Invalid number at position " . _hx_string_rec($start, "") . ": " . _hx_string_or_null(_hx_substr($this->str, $start, $this->pos - $start)));
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
	function __toString() { return 'haxe.format.JsonParser'; }
}
function haxe_format_JsonParser_0(&$__hx__this, &$c, &$save) {
	{
		$index3 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index3,1));
	}
}
function haxe_format_JsonParser_1(&$__hx__this, &$c, &$save) {
	{
		$index4 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index4,1));
	}
}
function haxe_format_JsonParser_2(&$__hx__this, &$c, &$save) {
	{
		$index5 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index5,1));
	}
}
function haxe_format_JsonParser_3(&$__hx__this, &$c, &$save1) {
	{
		$index6 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index6,1));
	}
}
function haxe_format_JsonParser_4(&$__hx__this, &$c, &$save1) {
	{
		$index7 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index7,1));
	}
}
function haxe_format_JsonParser_5(&$__hx__this, &$c, &$save1) {
	{
		$index8 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index8,1));
	}
}
function haxe_format_JsonParser_6(&$__hx__this, &$c, &$save1) {
	{
		$index9 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index9,1));
	}
}
function haxe_format_JsonParser_7(&$__hx__this, &$c, &$save2) {
	{
		$index10 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index10,1));
	}
}
function haxe_format_JsonParser_8(&$__hx__this, &$c, &$save2) {
	{
		$index11 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index11,1));
	}
}
function haxe_format_JsonParser_9(&$__hx__this, &$c, &$save2) {
	{
		$index12 = $__hx__this->pos++;
		return ord(substr($__hx__this->str,$index12,1));
	}
}
