<?php

class coopy_Unit {
	public function __construct($l = null, $r = null, $p = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.Unit::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($p === null) {
			$p = -2;
		}
		if($r === null) {
			$r = -2;
		}
		if($l === null) {
			$l = -2;
		}
		$this->l = $l;
		$this->r = $r;
		$this->p = $p;
		$GLOBALS['%s']->pop();
	}}
	public $l;
	public $r;
	public $p;
	public function lp() {
		$GLOBALS['%s']->push("coopy.Unit::lp");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->p === -2) {
			$tmp = $this->l;
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = $this->p;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toString() {
		$GLOBALS['%s']->push("coopy.Unit::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->p >= -1) {
			$tmp = _hx_string_or_null(coopy_Unit::describe($this->p)) . "|" . _hx_string_or_null(coopy_Unit::describe($this->l)) . ":" . _hx_string_or_null(coopy_Unit::describe($this->r));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$tmp = _hx_string_or_null(coopy_Unit::describe($this->l)) . ":" . _hx_string_or_null(coopy_Unit::describe($this->r));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function fromString($txt) {
		$GLOBALS['%s']->push("coopy.Unit::fromString");
		$__hx__spos = $GLOBALS['%s']->length;
		$txt .= "]";
		$at = 0;
		{
			$_g1 = 0;
			$_g = strlen($txt);
			while($_g1 < $_g) {
				$i = $_g1++;
				$ch = _hx_char_code_at($txt, $i);
				if($ch >= 48 && $ch <= 57) {
					$at *= 10;
					$at += $ch - 48;
				} else {
					if($ch === 45) {
						$at = -1;
					} else {
						if($ch === 124) {
							$this->p = $at;
							$at = 0;
						} else {
							if($ch === 58) {
								$this->l = $at;
								$at = 0;
							} else {
								if($ch === 93) {
									$this->r = $at;
									{
										$GLOBALS['%s']->pop();
										return true;
									}
								}
							}
						}
					}
				}
				unset($i,$ch);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return false;
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
	static function describe($i) {
		$GLOBALS['%s']->push("coopy.Unit::describe");
		$__hx__spos = $GLOBALS['%s']->length;
		if($i >= 0) {
			$tmp = "" . _hx_string_rec($i, "");
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$GLOBALS['%s']->pop();
			return "-";
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return $this->toString(); }
}
