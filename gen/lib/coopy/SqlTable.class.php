<?php

class coopy_SqlTable implements coopy_RowStream, coopy_Meta, coopy_Table{
	public function __construct($db, $name, $helper = null) {
		if(!php_Boot::$skip_constructor) {
		$this->db = $db;
		$this->name = $name;
		$this->helper = $helper;
		if($helper === null) {
			$this->helper = $db->getHelper();
		}
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
		} else {
			if($y === 0) {
				$y = -1;
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
		haxe_Log::trace("SqlTable cannot set cells yet", _hx_anonymous(array("fileName" => "SqlTable.hx", "lineNumber" => 115, "className" => "coopy.SqlTable", "methodName" => "setCell")));
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
		return -1;
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
		return $this;
	}
	public function alterColumns($columns) {
		$result = $this->helper->alterColumns($this->db, $this->name, $columns);
		$this->columns = null;
		return $result;
	}
	public function changeRow($rc) {
		if($this->helper === null) {
			haxe_Log::trace("No sql helper", _hx_anonymous(array("fileName" => "SqlTable.hx", "lineNumber" => 183, "className" => "coopy.SqlTable", "methodName" => "changeRow")));
			return false;
		}
		if($rc->action === "+++") {
			return $this->helper->insert($this->db, $this->name, $rc->val);
		} else {
			if($rc->action === "---") {
				return $this->helper->delete($this->db, $this->name, $rc->cond);
			} else {
				if($rc->action === "->") {
					return $this->helper->update($this->db, $this->name, $rc->cond, $rc->val);
				}
			}
		}
		return false;
	}
	public function asTable() {
		$pct = 3;
		$this->getColumns();
		$w = $this->columnNames->length;
		$mt = new coopy_SimpleTable($w + 1, $pct);
		$mt->setCell(0, 0, "@");
		$mt->setCell(0, 1, "type");
		$mt->setCell(0, 2, "key");
		{
			$_g = 0;
			while($_g < $w) {
				$x = $_g++;
				$i = $x + 1;
				$mt->setCell($i, 0, $this->columnNames[$x]);
				$mt->setCell($i, 1, _hx_array_get($this->columns, $x)->type_value);
				$mt->setCell($i, 2, ((_hx_array_get($this->columns, $x)->primary) ? "primary" : ""));
				unset($x,$i);
			}
		}
		return $mt;
	}
	public function useForColumnChanges() {
		return true;
	}
	public function useForRowChanges() {
		return true;
	}
	public function cloneMeta($table = null) {
		return null;
	}
	public function applyFlags($flags) {
		return false;
	}
	public function getDatabase() {
		return $this->db;
	}
	public function getRowStream() {
		$this->getColumns();
		$this->db->begin("SELECT * FROM " . _hx_string_or_null($this->getQuotedTableName()) . " ORDER BY ?", (new _hx_array(array($this->db->rowid()))), $this->columnNames);
		return $this;
	}
	public function isNested() {
		return false;
	}
	public function isSql() {
		return true;
	}
	public function fetchRow() {
		if($this->db->read()) {
			$row = new haxe_ds_StringMap();
			{
				$_g1 = 0;
				$_g = $this->columnNames->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					{
						$v = $this->db->get($i);
						$row->set($this->columnNames[$i], $v);
						$v;
						unset($v);
					}
					unset($i);
				}
			}
			return $row;
		}
		$this->db->end();
		return null;
	}
	public function fetchColumns() {
		$this->getColumns();
		return $this->columnNames;
	}
	public function getName() {
		return $this->name->toString();
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
