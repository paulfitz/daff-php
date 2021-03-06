<?php

class coopy_FlatCellBuilder implements coopy_CellBuilder{
	public function __construct($flags) {
		if(!php_Boot::$skip_constructor) {
		$this->flags = $flags;
	}}
	public $view;
	public $separator;
	public $conflict_separator;
	public $flags;
	public function needSeparator() {
		return true;
	}
	public function setSeparator($separator) {
		$this->separator = $separator;
	}
	public function setConflictSeparator($separator) {
		$this->conflict_separator = $separator;
	}
	public function setView($view) {
		$this->view = $view;
	}
	public function update($local, $remote) {
		return $this->view->toDatum(_hx_string_or_null(coopy_FlatCellBuilder::quoteForDiff($this->view, $local)) . _hx_string_or_null($this->separator) . _hx_string_or_null(coopy_FlatCellBuilder::quoteForDiff($this->view, $remote)));
	}
	public function conflict($parent, $local, $remote) {
		return _hx_string_or_null($this->view->toString($parent)) . _hx_string_or_null($this->conflict_separator) . _hx_string_or_null($this->view->toString($local)) . _hx_string_or_null($this->conflict_separator) . _hx_string_or_null($this->view->toString($remote));
	}
	public function marker($label) {
		return $this->view->toDatum($label);
	}
	public function links($unit, $row_like) {
		if($this->flags->count_like_a_spreadsheet && !$row_like) {
			return $this->view->toDatum($unit->toBase26String());
		}
		return $this->view->toDatum($unit->toString());
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
	static function quoteForDiff($v, $d) {
		$nil = "NULL";
		if($v->equals($d, null)) {
			return $nil;
		}
		$str = $v->toString($d);
		$score = 0;
		{
			$_g1 = 0;
			$_g = strlen($str);
			while($_g1 < $_g) {
				$i = $_g1++;
				if(_hx_char_code_at($str, $score) !== 95) {
					break;
				}
				$score++;
				unset($i);
			}
		}
		if(_hx_substr($str, $score, null) === $nil) {
			$str = "_" . _hx_string_or_null($str);
		}
		return $str;
	}
	function __toString() { return 'coopy.FlatCellBuilder'; }
}
