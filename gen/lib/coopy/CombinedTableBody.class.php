<?php

class coopy_CombinedTableBody implements coopy_Table{
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
	public $meta;
	public function getTable() {
		return $this;
	}
	public function get_width() {
		return $this->all->get_width() - 1;
	}
	public function get_height() {
		return $this->all->get_height() - $this->dy + 1;
	}
	public function getCell($x, $y) {
		if($y === 0) {
			if($this->meta === null) {
				$this->meta = $this->parent->getMeta()->asTable();
			}
			return $this->meta->getCell($x + $this->dx, 0);
		}
		return $this->all->getCell($x + $this->dx, $y + $this->dy - 1);
	}
	public function setCell($x, $y, $c) {
		if($y === 0) {
			$this->all->setCell($x + $this->dx, 0, $c);
			return;
		}
		$this->all->setCell($x + $this->dx, $y + $this->dy - 1, $c);
	}
	public function toString() {
		return coopy_SimpleTable::tableToString($this);
	}
	public function getCellView() {
		return $this->all->getCellView();
	}
	public function isResizable() {
		return $this->all->isResizable();
	}
	public function resize($w, $h) {
		return $this->all->resize($w + 1, $h + $this->dy);
	}
	public function clear() {
		$this->all->clear();
		$this->dx = 0;
		$this->dy = 0;
	}
	public function insertOrDeleteRows($fate, $hfate) {
		$fate2 = new _hx_array(array());
		{
			$_g1 = 0;
			$_g = $this->dy;
			while($_g1 < $_g) {
				$y = $_g1++;
				$fate2->push($y);
				unset($y);
			}
		}
		$hdr = true;
		{
			$_g2 = 0;
			while($_g2 < $fate->length) {
				$f = $fate[$_g2];
				++$_g2;
				if($hdr) {
					$hdr = false;
					continue;
				}
				$fate2->push(coopy_CombinedTableBody_0($this, $_g2, $f, $fate, $fate2, $hdr, $hfate));
				unset($f);
			}
		}
		return $this->all->insertOrDeleteRows($fate2, $hfate + $this->dy - 1);
	}
	public function insertOrDeleteColumns($fate, $wfate) {
		$fate2 = new _hx_array(array());
		{
			$_g1 = 0;
			$_g = $this->dx + 1;
			while($_g1 < $_g) {
				$x = $_g1++;
				$fate2->push($x);
				unset($x);
			}
		}
		{
			$_g2 = 0;
			while($_g2 < $fate->length) {
				$f = $fate[$_g2];
				++$_g2;
				$fate2->push(coopy_CombinedTableBody_1($this, $_g2, $f, $fate, $fate2, $wfate));
				unset($f);
			}
		}
		return $this->all->insertOrDeleteColumns($fate2, $wfate + $this->dx);
	}
	public function trimBlank() {
		return $this->all->trimBlank();
	}
	public function getData() {
		return null;
	}
	public function hclone() {
		return new coopy_CombinedTable($this->all->hclone());
	}
	public function create() {
		return new coopy_CombinedTable($this->all->create());
	}
	public function getMeta() {
		return $this->parent->getMeta();
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
function coopy_CombinedTableBody_0(&$__hx__this, &$_g2, &$f, &$fate, &$fate2, &$hdr, &$hfate) {
	if($f >= 0) {
		return $f + $__hx__this->dy - 1;
	} else {
		return $f;
	}
}
function coopy_CombinedTableBody_1(&$__hx__this, &$_g2, &$f, &$fate, &$fate2, &$wfate) {
	if($f >= 0) {
		return $f + $__hx__this->dx + 1;
	} else {
		return $f;
	}
}
