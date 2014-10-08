<?php

class coopy_Unit {
	public function __construct($l = null, $r = null, $p = null) {
		if(!php_Boot::$skip_constructor) {
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
	}}
	public $l;
	public $r;
	public $p;
	public function lp() {
		if($this->p === -2) {
			return $this->l;
		} else {
			return $this->p;
		}
	}
	public function toString() {
		if($this->p >= -1) {
			return _hx_string_or_null(coopy_Unit::describe($this->p)) . "|" . _hx_string_or_null(coopy_Unit::describe($this->l)) . ":" . _hx_string_or_null(coopy_Unit::describe($this->r));
		}
		return _hx_string_or_null(coopy_Unit::describe($this->l)) . ":" . _hx_string_or_null(coopy_Unit::describe($this->r));
	}
	public function fromString($txt) {
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
									return true;
								}
							}
						}
					}
				}
				unset($i,$ch);
			}
		}
		return false;
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
		if($i >= 0) {
			return "" . _hx_string_rec($i, "");
		} else {
			return "-";
		}
	}
	function __toString() { return $this->toString(); }
}
