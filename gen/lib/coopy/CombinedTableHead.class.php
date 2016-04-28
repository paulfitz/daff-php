<?php

class coopy_CombinedTableHead implements coopy_Table{
	public function __construct($parent, $dx, $dy) {
		if(!php_Boot::$skip_constructor) {
		$this->parent = $parent;
		$this->dx = $dx;
		$this->dy = $dy;
		$this->all = $parent->all();
	}}
	public $parent;
	public $dx;
	public $dy;
	public $all;
	public function getTable() {
		return $this;
	}
	public function get_width() {
		return $this->all->get_width();
	}
	public function get_height() {
		return $this->dy;
	}
	public function getCell($x, $y) {
		if($x === 0) {
			$v = $this->getCellView();
			$txt = $v->toString($this->all->getCell($x, $y));
			if(_hx_char_at($txt, 0) === "@") {
				return _hx_substr($txt, 1, strlen($txt));
			}
		}
		return $this->all->getCell($x, $y);
	}
	public function setCell($x, $y, $c) {
		$this->all->setCell($x, $y, $c);
	}
	public function toString() {
		return coopy_SimpleTable::tableToString($this);
	}
	public function getCellView() {
		return $this->all->getCellView();
	}
	public function isResizable() {
		return false;
	}
	public function resize($w, $h) {
		return false;
	}
	public function clear() {}
	public function insertOrDeleteRows($fate, $hfate) {
		return false;
	}
	public function insertOrDeleteColumns($fate, $wfate) {
		return $this->all->insertOrDeleteColumns($fate, $wfate);
	}
	public function trimBlank() {
		return false;
	}
	public function getData() {
		return null;
	}
	public function hclone() {
		return null;
	}
	public function create() {
		return null;
	}
	public function getMeta() {
		return null;
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
	static $__properties__ = array("get_width" => "get_width","get_height" => "get_height");
	function __toString() { return $this->toString(); }
}
