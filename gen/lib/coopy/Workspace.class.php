<?php

class coopy_Workspace {
	public function __construct() {}
	public $parent;
	public $local;
	public $remote;
	public $report;
	public $tparent;
	public $tlocal;
	public $tremote;
	public $p2l;
	public $p2r;
	public $l2r;
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
	function __toString() { return 'coopy.Workspace'; }
}
