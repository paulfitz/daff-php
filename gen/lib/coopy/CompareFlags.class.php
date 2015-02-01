<?php

class coopy_CompareFlags {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->ordered = true;
		$this->show_unchanged = false;
		$this->unchanged_context = 1;
		$this->always_show_order = false;
		$this->never_show_order = true;
		$this->show_unchanged_columns = false;
		$this->unchanged_column_context = 1;
		$this->always_show_header = true;
		$this->acts = null;
		$this->ids = null;
		$this->columns_to_ignore = null;
		$this->allow_nested_cells = false;
	}}
	public $ordered;
	public $show_unchanged;
	public $unchanged_context;
	public $always_show_order;
	public $never_show_order;
	public $show_unchanged_columns;
	public $unchanged_column_context;
	public $always_show_header;
	public $acts;
	public $ids;
	public $columns_to_ignore;
	public $allow_nested_cells;
	public function filter($act, $allow) {
		if($this->acts === null) {
			$this->acts = new haxe_ds_StringMap();
			$this->acts->set("update", !$allow);
			$this->acts->set("insert", !$allow);
			$this->acts->set("delete", !$allow);
		}
		if(!$this->acts->exists($act)) {
			return false;
		}
		$this->acts->set($act, $allow);
		return true;
	}
	public function allowUpdate() {
		if($this->acts === null) {
			return true;
		}
		return $this->acts->exists("update");
	}
	public function allowInsert() {
		if($this->acts === null) {
			return true;
		}
		return $this->acts->exists("insert");
	}
	public function allowDelete() {
		if($this->acts === null) {
			return true;
		}
		return $this->acts->exists("delete");
	}
	public function getIgnoredColumns() {
		if($this->columns_to_ignore === null) {
			return null;
		}
		$ignore = new haxe_ds_StringMap();
		{
			$_g1 = 0;
			$_g = $this->columns_to_ignore->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$ignore->set($this->columns_to_ignore[$i], true);
				unset($i);
			}
		}
		return $ignore;
	}
	public function addPrimaryKey($column) {
		if($this->ids === null) {
			$this->ids = new _hx_array(array());
		}
		$this->ids->push($column);
	}
	public function ignoreColumn($column) {
		if($this->columns_to_ignore === null) {
			$this->columns_to_ignore = new _hx_array(array());
		}
		$this->columns_to_ignore->push($column);
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
	function __toString() { return 'coopy.CompareFlags'; }
}
