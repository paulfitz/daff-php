<?php

class coopy_Tables {
	public function __construct($template) {
		if(!php_Boot::$skip_constructor) {
		$this->template = $template;
		$this->tables = new haxe_ds_StringMap();
		$this->table_order = new _hx_array(array());
	}}
	public $template;
	public $tables;
	public $table_order;
	public $alignment;
	public function add($name) {
		$t = $this->template->hclone();
		$this->tables->set($name, $t);
		$this->table_order->push($name);
		return $t;
	}
	public function getOrder() {
		return $this->table_order;
	}
	public function get($name) {
		return $this->tables->get($name);
	}
	public function one() {
		return $this->tables->get($this->table_order[0]);
	}
	public function hasInsDel() {
		if($this->alignment === null) {
			return false;
		}
		if($this->alignment->has_addition) {
			return true;
		}
		if($this->alignment->has_removal) {
			return true;
		}
		return false;
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
	function __toString() { return 'coopy.Tables'; }
}
