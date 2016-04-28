<?php

class coopy_TableComparisonState {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->reset();
	}}
	public $p;
	public $a;
	public $b;
	public $completed;
	public $run_to_completion;
	public $is_equal;
	public $is_equal_known;
	public $has_same_columns;
	public $has_same_columns_known;
	public $compare_flags;
	public $p_meta;
	public $a_meta;
	public $b_meta;
	public $alignment;
	public $children;
	public $child_order;
	public function reset() {
		$this->completed = false;
		$this->run_to_completion = true;
		$this->is_equal_known = false;
		$this->is_equal = false;
		$this->has_same_columns = false;
		$this->has_same_columns_known = false;
		$this->compare_flags = null;
		$this->alignment = null;
		$this->children = null;
		$this->child_order = null;
	}
	public function getMeta() {
		if($this->p !== null && $this->p_meta === null) {
			$this->p_meta = $this->p->getMeta();
		}
		if($this->a !== null && $this->a_meta === null) {
			$this->a_meta = $this->a->getMeta();
		}
		if($this->b !== null && $this->b_meta === null) {
			$this->b_meta = $this->b->getMeta();
		}
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
	function __toString() { return 'coopy.TableComparisonState'; }
}
