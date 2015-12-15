<?php

class coopy_CombinedTable implements coopy_Table{
	public function __construct($t) {
		if(!php_Boot::$skip_constructor) {
		$this->t = $t;
		$this->dx = 0;
		$this->dy = 0;
		$this->core = $t;
		$this->head = null;
		if($t->get_width() < 1 || $t->get_height() < 1) {
			return;
		}
		$v = $t->getCellView();
		if($v->toString($t->getCell(0, 0)) !== "@@") {
			return;
		}
		$this->dx = 1;
		$this->dy = 0;
		{
			$_g1 = 0;
			$_g = $t->get_height();
			while($_g1 < $_g) {
				$y = $_g1++;
				$txt = $v->toString($t->getCell(0, $y));
				if($txt === null || $txt === "" || $txt === "null") {
					break;
				}
				$this->dy++;
				unset($y,$txt);
			}
		}
		$this->head = new coopy_CombinedTableHead($this, $this->dx, $this->dy);
		$this->body = new coopy_CombinedTableBody($this, $this->dx, $this->dy);
		$this->core = $this->body;
		$this->meta = new coopy_SimpleMeta($this->head, null);
	}}
	public $t;
	public $body;
	public $head;
	public $dx;
	public $dy;
	public $core;
	public $meta;
	public function all() {
		return $this->t;
	}
	public function getTable() {
		return $this;
	}
	public function get_width() {
		return $this->core->get_width();
	}
	public function get_height() {
		return $this->core->get_height();
	}
	public function getCell($x, $y) {
		return $this->core->getCell($x, $y);
	}
	public function setCell($x, $y, $c) {
		$this->core->setCell($x, $y, $c);
	}
	public function toString() {
		return coopy_SimpleTable::tableToString($this);
	}
	public function getCellView() {
		return $this->t->getCellView();
	}
	public function isResizable() {
		return $this->core->isResizable();
	}
	public function resize($w, $h) {
		return $this->core->resize($h, $w);
	}
	public function clear() {
		$this->core->clear();
	}
	public function insertOrDeleteRows($fate, $hfate) {
		return $this->core->insertOrDeleteRows($fate, $hfate);
	}
	public function insertOrDeleteColumns($fate, $wfate) {
		return $this->core->insertOrDeleteColumns($fate, $wfate);
	}
	public function trimBlank() {
		return $this->core->trimBlank();
	}
	public function getData() {
		return null;
	}
	public function hclone() {
		return $this->core->hclone();
	}
	public function getMeta() {
		return $this->meta;
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
