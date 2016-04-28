<?php

class coopy_JsonTable implements coopy_Meta, coopy_Table{
	public function __construct($data, $name) {
		if(!php_Boot::$skip_constructor) {
		$this->data = $data;
		$this->columns = Reflect::field($data, "columns");
		$this->rows = Reflect::field($data, "rows");
		$this->w = $this->columns->length;
		$this->h = $this->rows->length;
		$this->idx2col = new haxe_ds_IntMap();
		{
			$_g1 = 0;
			$_g = $this->columns->length;
			while($_g1 < $_g) {
				$idx = $_g1++;
				{
					$v = $this->columns[$idx];
					$this->idx2col->set($idx, $v);
					$v;
					unset($v);
				}
				unset($idx);
			}
		}
		$this->name = $name;
	}}
	public $w;
	public $h;
	public $columns;
	public $rows;
	public $data;
	public $idx2col;
	public $name;
	public function getTable() {
		return $this;
	}
	public function get_width() {
		return $this->w;
	}
	public function get_height() {
		return $this->h + 1;
	}
	public function getCell($x, $y) {
		if($y === 0) {
			return $this->idx2col->get($x);
		}
		return Reflect::field($this->rows[$y - 1], $this->idx2col->get($x));
	}
	public function setCell($x, $y, $c) {
		haxe_Log::trace("JsonTable is read-only", _hx_anonymous(array("fileName" => "JsonTable.hx", "lineNumber" => 52, "className" => "coopy.JsonTable", "methodName" => "setCell")));
	}
	public function toString() {
		return "";
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
	public function getData() {
		return null;
	}
	public function hclone() {
		return null;
	}
	public function setMeta($meta) {}
	public function getMeta() {
		return $this;
	}
	public function create() {
		return null;
	}
	public function alterColumns($columns) {
		return false;
	}
	public function changeRow($rc) {
		return false;
	}
	public function applyFlags($flags) {
		return false;
	}
	public function asTable() {
		return null;
	}
	public function cloneMeta($table = null) {
		return null;
	}
	public function useForColumnChanges() {
		return false;
	}
	public function useForRowChanges() {
		return false;
	}
	public function getRowStream() {
		return null;
	}
	public function isNested() {
		return false;
	}
	public function isSql() {
		return false;
	}
	public function getName() {
		return $this->name;
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
