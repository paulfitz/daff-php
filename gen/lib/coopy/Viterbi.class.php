<?php

class coopy_Viterbi {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.Viterbi::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->K = $this->T = 0;
		$this->reset();
		$this->cost = new coopy_SparseSheet();
		$this->src = new coopy_SparseSheet();
		$this->path = new coopy_SparseSheet();
		$GLOBALS['%s']->pop();
	}}
	public $K;
	public $T;
	public $index;
	public $mode;
	public $path_valid;
	public $best_cost;
	public $cost;
	public $src;
	public $path;
	public function reset() {
		$GLOBALS['%s']->push("coopy.Viterbi::reset");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->index = 0;
		$this->mode = 0;
		$this->path_valid = false;
		$this->best_cost = 0;
		$GLOBALS['%s']->pop();
	}
	public function setSize($states, $sequence_length) {
		$GLOBALS['%s']->push("coopy.Viterbi::setSize");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->K = $states;
		$this->T = $sequence_length;
		$this->cost->resize($this->K, $this->T, 0);
		$this->src->resize($this->K, $this->T, -1);
		$this->path->resize(1, $this->T, -1);
		$GLOBALS['%s']->pop();
	}
	public function assertMode($next) {
		$GLOBALS['%s']->push("coopy.Viterbi::assertMode");
		$__hx__spos = $GLOBALS['%s']->length;
		if($next === 0 && $this->mode === 1) {
			$this->index++;
		}
		$this->mode = $next;
		$GLOBALS['%s']->pop();
	}
	public function addTransition($s0, $s1, $c) {
		$GLOBALS['%s']->push("coopy.Viterbi::addTransition");
		$__hx__spos = $GLOBALS['%s']->length;
		$resize = false;
		if($s0 >= $this->K) {
			$this->K = $s0 + 1;
			$resize = true;
		}
		if($s1 >= $this->K) {
			$this->K = $s1 + 1;
			$resize = true;
		}
		if($resize) {
			$this->cost->nonDestructiveResize($this->K, $this->T, 0);
			$this->src->nonDestructiveResize($this->K, $this->T, -1);
			$this->path->nonDestructiveResize(1, $this->T, -1);
		}
		$this->path_valid = false;
		$this->assertMode(1);
		if($this->index >= $this->T) {
			$this->T = $this->index + 1;
			$this->cost->nonDestructiveResize($this->K, $this->T, 0);
			$this->src->nonDestructiveResize($this->K, $this->T, -1);
			$this->path->nonDestructiveResize(1, $this->T, -1);
		}
		$sourced = false;
		if($this->index > 0) {
			$c += $this->cost->get($s0, $this->index - 1);
			$sourced = $this->src->get($s0, $this->index - 1) !== -1;
		} else {
			$sourced = true;
		}
		if($sourced) {
			if($c < $this->cost->get($s1, $this->index) || $this->src->get($s1, $this->index) === -1) {
				$this->cost->set($s1, $this->index, $c);
				$this->src->set($s1, $this->index, $s0);
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function endTransitions() {
		$GLOBALS['%s']->push("coopy.Viterbi::endTransitions");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->path_valid = false;
		$this->assertMode(0);
		$GLOBALS['%s']->pop();
	}
	public function beginTransitions() {
		$GLOBALS['%s']->push("coopy.Viterbi::beginTransitions");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->path_valid = false;
		$this->assertMode(1);
		$GLOBALS['%s']->pop();
	}
	public function calculatePath() {
		$GLOBALS['%s']->push("coopy.Viterbi::calculatePath");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->path_valid) {
			$GLOBALS['%s']->pop();
			return;
		}
		$this->endTransitions();
		$best = 0;
		$bestj = -1;
		if($this->index <= 0) {
			$this->path_valid = true;
			{
				$GLOBALS['%s']->pop();
				return;
			}
		}
		{
			$_g1 = 0;
			$_g = $this->K;
			while($_g1 < $_g) {
				$j = $_g1++;
				if(($this->cost->get($j, $this->index - 1) < $best || $bestj === -1) && $this->src->get($j, $this->index - 1) !== -1) {
					$best = $this->cost->get($j, $this->index - 1);
					$bestj = $j;
				}
				unset($j);
			}
		}
		$this->best_cost = $best;
		{
			$_g11 = 0;
			$_g2 = $this->index;
			while($_g11 < $_g2) {
				$j1 = $_g11++;
				$i = $this->index - 1 - $j1;
				$this->path->set(0, $i, $bestj);
				if(!($bestj !== -1 && ($bestj >= 0 && $bestj < $this->K))) {
					haxe_Log::trace("Problem in Viterbi", _hx_anonymous(array("fileName" => "Viterbi.hx", "lineNumber" => 119, "className" => "coopy.Viterbi", "methodName" => "calculatePath")));
				}
				$bestj = $this->src->get($bestj, $i);
				unset($j1,$i);
			}
		}
		$this->path_valid = true;
		$GLOBALS['%s']->pop();
	}
	public function toString() {
		$GLOBALS['%s']->push("coopy.Viterbi::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->calculatePath();
		$txt = "";
		{
			$_g1 = 0;
			$_g = $this->index;
			while($_g1 < $_g) {
				$i = $_g1++;
				if($this->path->get(0, $i) === -1) {
					$txt .= "*";
				} else {
					$txt .= _hx_string_rec($this->path->get(0, $i), "");
				}
				if($this->K >= 10) {
					$txt .= " ";
				}
				unset($i);
			}
		}
		$txt .= " costs " . _hx_string_rec($this->getCost(), "");
		{
			$GLOBALS['%s']->pop();
			return $txt;
		}
		$GLOBALS['%s']->pop();
	}
	public function length() {
		$GLOBALS['%s']->push("coopy.Viterbi::length");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->index > 0) {
			$this->calculatePath();
		}
		{
			$tmp = $this->index;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function get($i) {
		$GLOBALS['%s']->push("coopy.Viterbi::get");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->calculatePath();
		{
			$tmp = $this->path->get(0, $i);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getCost() {
		$GLOBALS['%s']->push("coopy.Viterbi::getCost");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->calculatePath();
		{
			$tmp = $this->best_cost;
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
	function __toString() { return $this->toString(); }
}
