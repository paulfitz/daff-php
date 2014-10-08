<?php

class coopy_Csv {
	public function __construct($delim = null) {
		if(!php_Boot::$skip_constructor) {
		if($delim === null) {
			$delim = ",";
		}
		$this->cursor = 0;
		$this->row_ended = false;
		if($delim === null) {
			$this->delim = ",";
		} else {
			$this->delim = $delim;
		}
	}}
	public $cursor;
	public $row_ended;
	public $has_structure;
	public $delim;
	public function renderTable($t) {
		$result = "";
		$w = $t->get_width();
		$h = $t->get_height();
		$txt = "";
		$v = $t->getCellView();
		{
			$_g = 0;
			while($_g < $h) {
				$y = $_g++;
				{
					$_g1 = 0;
					while($_g1 < $w) {
						$x = $_g1++;
						if($x > 0) {
							$txt .= _hx_string_or_null($this->delim);
						}
						$txt .= _hx_string_or_null($this->renderCell($v, $t->getCell($x, $y)));
						unset($x);
					}
					unset($_g1);
				}
				$txt .= "\x0D\x0A";
				unset($y);
			}
		}
		return $txt;
	}
	public function renderCell($v, $d) {
		if($d === null) {
			return "NULL";
		}
		$str = $v->toString($d);
		$need_quote = false;
		{
			$_g1 = 0;
			$_g = strlen($str);
			while($_g1 < $_g) {
				$i = $_g1++;
				$ch = _hx_char_at($str, $i);
				if($ch === "\"" || $ch === "'" || $ch === $this->delim || $ch === "\x0D" || $ch === "\x0A" || $ch === "\x09" || $ch === " ") {
					$need_quote = true;
					break;
				}
				unset($i,$ch);
			}
		}
		$result = "";
		if($need_quote) {
			$result .= "\"";
		}
		$line_buf = "";
		{
			$_g11 = 0;
			$_g2 = strlen($str);
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$ch1 = _hx_char_at($str, $i1);
				if($ch1 === "\"") {
					$result .= "\"";
				}
				if($ch1 !== "\x0D" && $ch1 !== "\x0A") {
					if(strlen($line_buf) > 0) {
						$result .= _hx_string_or_null($line_buf);
						$line_buf = "";
					}
					$result .= _hx_string_or_null($ch1);
				} else {
					$line_buf .= _hx_string_or_null($ch1);
				}
				unset($i1,$ch1);
			}
		}
		if($need_quote) {
			$result .= "\"";
		}
		return $result;
	}
	public function parseTable($txt) {
		$this->cursor = 0;
		$this->row_ended = false;
		$this->has_structure = true;
		$result = new _hx_array(array());
		$row = new _hx_array(array());
		while($this->cursor < strlen($txt)) {
			$cell = $this->parseCell($txt);
			$row->push($cell);
			if($this->row_ended) {
				$result->push($row);
				$row = new _hx_array(array());
			}
			$this->cursor++;
			unset($cell);
		}
		return $result;
	}
	public function parseCell($txt) {
		if($txt === null) {
			return null;
		}
		$this->row_ended = false;
		$first_non_underscore = strlen($txt);
		$last_processed = 0;
		$quoting = false;
		$quote = 0;
		$result = "";
		$start = $this->cursor;
		{
			$_g1 = $this->cursor;
			$_g = strlen($txt);
			while($_g1 < $_g) {
				$i = $_g1++;
				$ch = _hx_char_code_at($txt, $i);
				$last_processed = $i;
				if($ch !== 95 && $i < $first_non_underscore) {
					$first_non_underscore = $i;
				}
				if($this->has_structure) {
					if(!$quoting) {
						if($ch === _hx_char_code_at($this->delim, 0)) {
							break;
						}
						if($ch === 13 || $ch === 10) {
							$ch2 = _hx_char_code_at($txt, $i + 1);
							if($ch2 !== null) {
								if($ch2 !== $ch) {
									if($ch2 === 13 || $ch2 === 10) {
										$last_processed++;
									}
								}
							}
							$this->row_ended = true;
							break;
							unset($ch2);
						}
						if($ch === 34 || $ch === 39) {
							if($i === $this->cursor) {
								$quoting = true;
								$quote = $ch;
								if($i !== $start) {
									$result .= _hx_string_or_null(chr($ch));
								}
								continue;
							} else {
								if($ch === $quote) {
									$quoting = true;
								}
							}
						}
						$result .= _hx_string_or_null(chr($ch));
						continue;
					}
					if($ch === $quote) {
						$quoting = false;
						continue;
					}
				}
				$result .= _hx_string_or_null(chr($ch));
				unset($i,$ch);
			}
		}
		$this->cursor = $last_processed;
		if($quote === 0) {
			if($result === "NULL") {
				return null;
			}
			if($first_non_underscore > $start) {
				$del = $first_non_underscore - $start;
				if(_hx_substr($result, $del, null) === "NULL") {
					return _hx_substr($result, 1, null);
				}
			}
		}
		return $result;
	}
	public function parseSingleCell($txt) {
		$this->cursor = 0;
		$this->row_ended = false;
		$this->has_structure = false;
		return $this->parseCell($txt);
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
	function __toString() { return 'coopy.Csv'; }
}
