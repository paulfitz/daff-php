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
	public function reset() {
		$this->completed = false;
		$this->run_to_completion = true;
		$this->is_equal_known = false;
		$this->is_equal = false;
		$this->has_same_columns = false;
		$this->has_same_columns_known = false;
		$this->compare_flags = null;
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
