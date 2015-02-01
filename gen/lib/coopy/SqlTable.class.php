<?php

class coopy_SqlTable implements coopy_Table{
	public function __construct($db, $name, $helper = null) {
		if(!php_Boot::$skip_constructor) {
		$this->db = $db;
		$this->name = $name;
		$this->helper = $helper;
		$this->cache = new haxe_ds_IntMap();
		$this->h = -1;
		$this->id2rid = null;
		$this->getColumns();
	}}
	public $db;
	public $columns;
	public $name;
	public $quotedTableName;
	public $cache;
	public $columnNames;
	public $h;
	public $helper;
	public $id2rid;
	public function getColumns() {
		if($this->columns !== null) {
			return;
		}
		if($this->db === null) {
			return;
		}
		$this->columns = $this->db->getColumns($this->name);
		$this->columnNames = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = $this->columns;
			while($_g < $_g1->length) {
				$col = $_g1[$_g];
				++$_g;
				$this->columnNames->push($col->getName());
				unset($col);
			}
		}
	}
	public function getPrimaryKey() {
		$this->getColumns();
		$result = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = $this->columns;
			while($_g < $_g1->length) {
				$col = $_g1[$_g];
				++$_g;
				if(!$col->isPrimaryKey()) {
					continue;
				}
				$result->push($col->getName());
				unset($col);
			}
		}
		return $result;
	}
	public function getAllButPrimaryKey() {
		$this->getColumns();
		$result = new _hx_array(array());
		{
			$_g = 0;
			$_g1 = $this->columns;
			while($_g < $_g1->length) {
				$col = $_g1[$_g];
				++$_g;
				if($col->isPrimaryKey()) {
					continue;
				}
				$result->push($col->getName());
				unset($col);
			}
		}
		return $result;
	}
	public function getColumnNames() {
		$this->getColumns();
		return $this->columnNames;
	}
	public function getQuotedTableName() {
		if($this->quotedTableName !== null) {
			return $this->quotedTableName;
		}
		$this->quotedTableName = $this->db->getQuotedTableName($this->name);
		return $this->quotedTableName;
	}
	public function getQuotedColumnName($name) {
		return $this->db->getQuotedColumnName($name);
	}
	public function getCell($x, $y) {
		if($this->h >= 0) {
			$y = $y - 1;
			if($y >= 0) {
				$y = $this->id2rid[$y];
			}
		}
		if($y < 0) {
			$this->getColumns();
			return _hx_array_get($this->columns, $x)->name;
		}
		$row = $this->cache->get($y);
		if($row === null) {
			$row = new haxe_ds_IntMap();
			$this->getColumns();
			$this->db->beginRow($this->name, $y, $this->columnNames);
			while($this->db->read()) {
				$_g1 = 0;
				$_g = $this->get_width();
				while($_g1 < $_g) {
					$i = $_g1++;
					{
						$v = $this->db->get($i);
						$row->set($i, $v);
						$v;
						unset($v);
					}
					unset($i);
				}
				unset($_g1,$_g);
			}
			$this->db->end();
			{
				$this->cache->set($y, $row);
				$row;
			}
		}
		{
			$this1 = $this->cache->get($y);
			return $this1->get($x);
		}
	}
	public function setCellCache($x, $y, $c) {
		$row = $this->cache->get($y);
		if($row === null) {
			$row = new haxe_ds_IntMap();
			$this->getColumns();
			{
				$this->cache->set($y, $row);
				$row;
			}
		}
		{
			$v = $c;
			$row->set($x, $v);
			$v;
		}
	}
	public function setCell($x, $y, $c) {
		haxe_Log::trace("SqlTable cannot set cells yet", _hx_anonymous(array("fileName" => "SqlTable.hx", "lineNumber" => 112, "className" => "coopy.SqlTable", "methodName" => "setCell")));
	}
	public function getCellView() {
		return new coopy_SimpleView();
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
		$this->getColumns();
		return $this->columns->length;
	}
	public function get_height() {
		if($this->h >= 0) {
			return $this->h;
		}
		if($this->helper === null) {
			return -1;
		}
		$this->id2rid = $this->helper->getRowIDs($this->db, $this->name);
		$this->h = $this->id2rid->length + 1;
		return $this->h;
	}
	public function getData() {
		return null;
	}
	public function hclone() {
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
	function __toString() { return 'coopy.SqlTable'; }
}
