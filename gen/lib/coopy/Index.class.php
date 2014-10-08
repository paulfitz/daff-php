<?php

class coopy_Index {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->items = new haxe_ds_StringMap();
		$this->cols = new _hx_array(array());
		$this->keys = new _hx_array(array());
		$this->top_freq = 0;
		$this->height = 0;
	}}
	public $items;
	public $keys;
	public $top_freq;
	public $height;
	public $cols;
	public $v;
	public $indexed_table;
	public function addColumn($i) {
		$this->cols->push($i);
	}
	public function indexTable($t) {
		$this->indexed_table = $t;
		if($this->keys->length !== $t->get_height() && $t->get_height() > 0) {
			$this->keys[$t->get_height() - 1] = null;
		}
		{
			$_g1 = 0;
			$_g = $t->get_height();
			while($_g1 < $_g) {
				$i = $_g1++;
				$key = $this->keys[$i];
				if($key === null) {
					$this->keys[$i] = $key = $this->toKey($t, $i);
				}
				$item = $this->items->get($key);
				if($item === null) {
					$item = new coopy_IndexItem();
					$this->items->set($key, $item);
				}
				$ct = $item->add($i);
				if($ct > $this->top_freq) {
					$this->top_freq = $ct;
				}
				unset($key,$item,$i,$ct);
			}
		}
		$this->height = $t->get_height();
	}
	public function toKey($t, $i) {
		$wide = "";
		if($this->v === null) {
			$this->v = $t->getCellView();
		}
		{
			$_g1 = 0;
			$_g = $this->cols->length;
			while($_g1 < $_g) {
				$k = $_g1++;
				$d = $t->getCell($this->cols[$k], $i);
				$txt = $this->v->toString($d);
				if($txt === null || $txt === "" || $txt === "null" || $txt === "undefined") {
					continue;
				}
				if($k > 0) {
					$wide .= " // ";
				}
				$wide .= _hx_string_or_null($txt);
				unset($txt,$k,$d);
			}
		}
		return $wide;
	}
	public function toKeyByContent($row) {
		$wide = "";
		{
			$_g1 = 0;
			$_g = $this->cols->length;
			while($_g1 < $_g) {
				$k = $_g1++;
				$txt = $row->getRowString($this->cols[$k]);
				if($txt === null || $txt === "" || $txt === "null" || $txt === "undefined") {
					continue;
				}
				if($k > 0) {
					$wide .= " // ";
				}
				$wide .= _hx_string_or_null($txt);
				unset($txt,$k);
			}
		}
		return $wide;
	}
	public function getTable() {
		return $this->indexed_table;
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
	function __toString() { return 'coopy.Index'; }
}
