<?php

class coopy_JsonTables implements coopy_Table{
	public function __construct($json, $flags) {
		if(!php_Boot::$skip_constructor) {
		$this->db = $json;
		$names = Reflect::field($json, "names");
		$allowed = null;
		$count = $names->length;
		if($flags !== null && $flags->tables !== null) {
			$allowed = new haxe_ds_StringMap();
			{
				$_g = 0;
				$_g1 = $flags->tables;
				while($_g < $_g1->length) {
					$name = $_g1[$_g];
					++$_g;
					$allowed->set($name, true);
					unset($name);
				}
			}
			$count = 0;
			{
				$_g2 = 0;
				while($_g2 < $names->length) {
					$name1 = $names[$_g2];
					++$_g2;
					if($allowed->exists($name1)) {
						$count++;
					}
					unset($name1);
				}
			}
		}
		$this->t = new coopy_SimpleTable(2, $count + 1);
		$this->t->setCell(0, 0, "name");
		$this->t->setCell(1, 0, "table");
		$v = $this->t->getCellView();
		$at = 1;
		{
			$_g3 = 0;
			while($_g3 < $names->length) {
				$name2 = $names[$_g3];
				++$_g3;
				if($allowed !== null) {
					if(!$allowed->exists($name2)) {
						continue;
					}
				}
				$this->t->setCell(0, $at, $name2);
				$tab = Reflect::field($this->db, "tables");
				$tab = Reflect::field($tab, $name2);
				$this->t->setCell(1, $at, $v->wrapTable(new coopy_JsonTable($tab, $name2)));
				$at++;
				unset($tab,$name2);
			}
		}
	}}
	public $db;
	public $t;
	public $flags;
	public function getCell($x, $y) {
		return $this->t->getCell($x, $y);
	}
	public function setCell($x, $y, $c) {}
	public function getCellView() {
		return $this->t->getCellView();
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
		return false;
	}
	public function trimBlank() {
		return false;
	}
	public function get_width() {
		return $this->t->get_width();
	}
	public function get_height() {
		return $this->t->get_height();
	}
	public function getData() {
		return null;
	}
	public function hclone() {
		return null;
	}
	public function getMeta() {
		return new coopy_SimpleMeta($this, true, true);
	}
	public function create() {
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
	function __toString() { return 'coopy.JsonTables'; }
}
