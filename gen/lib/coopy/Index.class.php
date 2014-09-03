<?php

class coopy_Index {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.Index::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->items = new haxe_ds_StringMap();
		$this->cols = new _hx_array(array());
		$this->keys = new _hx_array(array());
		$this->top_freq = 0;
		$this->height = 0;
		$GLOBALS['%s']->pop();
	}}
	public $items;
	public $keys;
	public $top_freq;
	public $height;
	public $cols;
	public $v;
	public $indexed_table;
	public function addColumn($i) {
		$GLOBALS['%s']->push("coopy.Index::addColumn");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->cols->push($i);
		$GLOBALS['%s']->pop();
	}
	public function indexTable($t) {
		$GLOBALS['%s']->push("coopy.Index::indexTable");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->indexed_table = $t;
		{
			$_g1 = 0;
			$_g = $t->get_height();
			while($_g1 < $_g) {
				$i = $_g1++;
				$key = null;
				if($this->keys->length > $i) {
					$key = $this->keys[$i];
				} else {
					$key = $this->toKey($t, $i);
					$this->keys->push($key);
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
		$GLOBALS['%s']->pop();
	}
	public function toKey($t, $i) {
		$GLOBALS['%s']->push("coopy.Index::toKey");
		$__hx__spos = $GLOBALS['%s']->length;
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
		{
			$GLOBALS['%s']->pop();
			return $wide;
		}
		$GLOBALS['%s']->pop();
	}
	public function toKeyByContent($row) {
		$GLOBALS['%s']->push("coopy.Index::toKeyByContent");
		$__hx__spos = $GLOBALS['%s']->length;
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
		{
			$GLOBALS['%s']->pop();
			return $wide;
		}
		$GLOBALS['%s']->pop();
	}
	public function getTable() {
		$GLOBALS['%s']->push("coopy.Index::getTable");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->indexed_table;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
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
