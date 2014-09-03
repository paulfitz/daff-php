<?php

class coopy_IndexPair {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.IndexPair::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ia = new coopy_Index();
		$this->ib = new coopy_Index();
		$this->quality = 0;
		$GLOBALS['%s']->pop();
	}}
	public $ia;
	public $ib;
	public $quality;
	public function addColumn($i) {
		$GLOBALS['%s']->push("coopy.IndexPair::addColumn");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ia->addColumn($i);
		$this->ib->addColumn($i);
		$GLOBALS['%s']->pop();
	}
	public function addColumns($ca, $cb) {
		$GLOBALS['%s']->push("coopy.IndexPair::addColumns");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ia->addColumn($ca);
		$this->ib->addColumn($cb);
		$GLOBALS['%s']->pop();
	}
	public function indexTables($a, $b) {
		$GLOBALS['%s']->push("coopy.IndexPair::indexTables");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ia->indexTable($a);
		$this->ib->indexTable($b);
		$good = 0;
		if(null == $this->ia->items) throw new HException('null iterable');
		$__hx__it = $this->ia->items->keys();
		while($__hx__it->hasNext()) {
			unset($key);
			$key = $__hx__it->next();
			$item_a = $this->ia->items->get($key);
			$spot_a = $item_a->lst->length;
			$item_b = $this->ib->items->get($key);
			$spot_b = 0;
			if($item_b !== null) {
				$spot_b = $item_b->lst->length;
			}
			if($spot_a === 1 && $spot_b === 1) {
				$good++;
			}
			unset($spot_b,$spot_a,$item_b,$item_a);
		}
		$this->quality = $good / Math::max(1.0, $a->get_height());
		$GLOBALS['%s']->pop();
	}
	public function queryByKey($ka) {
		$GLOBALS['%s']->push("coopy.IndexPair::queryByKey");
		$__hx__spos = $GLOBALS['%s']->length;
		$result = new coopy_CrossMatch();
		$result->item_a = $this->ia->items->get($ka);
		$result->item_b = $this->ib->items->get($ka);
		$result->spot_a = $result->spot_b = 0;
		if($ka !== "") {
			if($result->item_a !== null) {
				$result->spot_a = $result->item_a->lst->length;
			}
			if($result->item_b !== null) {
				$result->spot_b = $result->item_b->lst->length;
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $result;
		}
		$GLOBALS['%s']->pop();
	}
	public function queryByContent($row) {
		$GLOBALS['%s']->push("coopy.IndexPair::queryByContent");
		$__hx__spos = $GLOBALS['%s']->length;
		$result = new coopy_CrossMatch();
		$ka = $this->ia->toKeyByContent($row);
		{
			$tmp = $this->queryByKey($ka);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function queryLocal($row) {
		$GLOBALS['%s']->push("coopy.IndexPair::queryLocal");
		$__hx__spos = $GLOBALS['%s']->length;
		$ka = $this->ia->toKey($this->ia->getTable(), $row);
		{
			$tmp = $this->queryByKey($ka);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getTopFreq() {
		$GLOBALS['%s']->push("coopy.IndexPair::getTopFreq");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->ib->top_freq > $this->ia->top_freq) {
			$tmp = $this->ib->top_freq;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$tmp = $this->ia->top_freq;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getQuality() {
		$GLOBALS['%s']->push("coopy.IndexPair::getQuality");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->quality;
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
	function __toString() { return 'coopy.IndexPair'; }
}
