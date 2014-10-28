<?php

class coopy_IndexPair {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->ia = new coopy_Index();
		$this->ib = new coopy_Index();
		$this->quality = 0;
	}}
	public $ia;
	public $ib;
	public $quality;
	public function addColumns($ca, $cb) {
		$this->ia->addColumn($ca);
		$this->ib->addColumn($cb);
	}
	public function indexTables($a, $b) {
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
	}
	public function queryByKey($ka) {
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
		return $result;
	}
	public function queryByContent($row) {
		$result = new coopy_CrossMatch();
		$ka = $this->ia->toKeyByContent($row);
		return $this->queryByKey($ka);
	}
	public function queryLocal($row) {
		$ka = $this->ia->toKey($this->ia->getTable(), $row);
		return $this->queryByKey($ka);
	}
	public function localKey($row) {
		return $this->ia->toKey($this->ia->getTable(), $row);
	}
	public function remoteKey($row) {
		return $this->ib->toKey($this->ib->getTable(), $row);
	}
	public function getTopFreq() {
		if($this->ib->top_freq > $this->ia->top_freq) {
			return $this->ib->top_freq;
		}
		return $this->ia->top_freq;
	}
	public function getQuality() {
		return $this->quality;
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
