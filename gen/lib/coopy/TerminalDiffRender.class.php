<?php

class coopy_TerminalDiffRender {
	public function __construct($flags = null) {
		if(!php_Boot::$skip_constructor) {
		$this->align_columns = true;
		$this->wide_columns = false;
		$this->flags = $flags;
		if($flags !== null) {
			if($flags->padding_strategy === "dense") {
				$this->align_columns = false;
			}
			if($flags->padding_strategy === "sparse") {
				$this->wide_columns = true;
			}
		}
	}}
	public $codes;
	public $t;
	public $csv;
	public $v;
	public $align_columns;
	public $wide_columns;
	public $flags;
	public function alignColumns($enable) {
		$this->align_columns = $enable;
	}
	public function render($t) {
		$this->csv = new coopy_Csv(null);
		$result = "";
		$w = $t->get_width();
		$h = $t->get_height();
		$txt = "";
		$this->t = $t;
		$this->v = $t->getCellView();
		$this->codes = new haxe_ds_StringMap();
		$this->codes->set("header", "\x1B[0;1m");
		$this->codes->set("meta", "\x1B[0;1m");
		$this->codes->set("spec", "\x1B[35;1m");
		$this->codes->set("add", "\x1B[32;1m");
		$this->codes->set("conflict", "\x1B[33;1m");
		$this->codes->set("modify", "\x1B[34;1m");
		$this->codes->set("remove", "\x1B[31;1m");
		$this->codes->set("minor", "\x1B[2m");
		$this->codes->set("done", "\x1B[0m");
		$sizes = null;
		if($this->align_columns) {
			$sizes = $this->pickSizes($t);
		}
		{
			$_g = 0;
			while($_g < $h) {
				$y = $_g++;
				$target = 0;
				$at = 0;
				{
					$_g1 = 0;
					while($_g1 < $w) {
						$x = $_g1++;
						if($x > 0) {
							$txt .= _hx_string_or_null($this->codes->get("minor")) . "," . _hx_string_or_null($this->codes->get("done"));
						}
						if($sizes !== null) {
							$spaces = $target - $at;
							{
								$_g2 = 0;
								while($_g2 < $spaces) {
									$i = $_g2++;
									$txt .= " ";
									$at++;
									unset($i);
								}
								unset($_g2);
							}
							unset($spaces);
						}
						$txt .= _hx_string_or_null($this->getText($x, $y, true));
						if($sizes !== null) {
							$bit = $this->getText($x, $y, false);
							$at += strlen($bit);
							$target += $sizes[$x];
							unset($bit);
						}
						unset($x);
					}
					unset($_g1);
				}
				$txt .= "\x0D\x0A";
				unset($y,$target,$at);
			}
		}
		$this->t = null;
		$this->v = null;
		$this->csv = null;
		$this->codes = null;
		return $txt;
	}
	public function getText($x, $y, $color) {
		$val = $this->t->getCell($x, $y);
		$cell = coopy_DiffRender::renderCell($this->t, $this->v, $x, $y);
		if($color) {
			$code = null;
			if($cell->category !== null) {
				$code = $this->codes->get($cell->category);
			}
			if($cell->category_given_tr !== null) {
				$code_tr = $this->codes->get($cell->category_given_tr);
				if($code_tr !== null) {
					$code = $code_tr;
				}
			}
			if($code !== null) {
				if($cell->rvalue !== null) {
					$val = _hx_string_or_null($this->codes->get("remove")) . _hx_string_or_null($cell->lvalue) . _hx_string_or_null($this->codes->get("modify")) . _hx_string_or_null($cell->pretty_separator) . _hx_string_or_null($this->codes->get("add")) . _hx_string_or_null($cell->rvalue) . _hx_string_or_null($this->codes->get("done"));
					if($cell->pvalue !== null) {
						$val = _hx_string_or_null($this->codes->get("conflict")) . _hx_string_or_null($cell->pvalue) . _hx_string_or_null($this->codes->get("modify")) . _hx_string_or_null($cell->pretty_separator) . Std::string($val);
					}
				} else {
					$val = $cell->pretty_value;
					$val = _hx_string_or_null($code) . Std::string($val) . _hx_string_or_null($this->codes->get("done"));
				}
			}
		} else {
			$val = $cell->pretty_value;
		}
		return $this->csv->renderCell($this->v, $val);
	}
	public function pickSizes($t) {
		$w = $t->get_width();
		$h = $t->get_height();
		$v = $t->getCellView();
		$csv = new coopy_Csv(null);
		$sizes = new _hx_array(array());
		$row = -1;
		$total = $w - 1;
		{
			$_g = 0;
			while($_g < $w) {
				$x = $_g++;
				$m = 0;
				$m2 = 0;
				$mmax = 0;
				$mmostmax = 0;
				$mmin = -1;
				{
					$_g1 = 0;
					while($_g1 < $h) {
						$y = $_g1++;
						$txt = $this->getText($x, $y, false);
						if($txt === "@@" && $row === -1) {
							$row = $y;
						}
						$len = strlen($txt);
						if($y === $row) {
							$mmin = $len;
						}
						$m += $len;
						$m2 += $len * $len;
						if($len > $mmax) {
							$mmax = $len;
						}
						unset($y,$txt,$len);
					}
					unset($_g1);
				}
				$mean = $m / $h;
				$stddev = Math::sqrt($m2 / $h - $mean * $mean);
				$most = Std::int($mean + $stddev * 2 + 0.5);
				{
					$_g11 = 0;
					while($_g11 < $h) {
						$y1 = $_g11++;
						$txt1 = $this->getText($x, $y1, false);
						$len1 = strlen($txt1);
						if($len1 <= $most) {
							if($len1 > $mmostmax) {
								$mmostmax = $len1;
							}
						}
						unset($y1,$txt1,$len1);
					}
					unset($_g11);
				}
				$full = $mmax;
				$most = $mmostmax;
				if($mmin !== -1) {
					if($most < $mmin) {
						$most = $mmin;
					}
				}
				if($this->wide_columns) {
					$most = $full;
				}
				$sizes->push($most);
				$total += $most;
				unset($x,$stddev,$most,$mmostmax,$mmin,$mmax,$mean,$m2,$m,$full);
			}
		}
		if($total > 130) {
			return null;
		}
		return $sizes;
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
	function __toString() { return 'coopy.TerminalDiffRender'; }
}
