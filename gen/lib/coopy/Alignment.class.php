<?php

class coopy_Alignment {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->map_a2b = new haxe_ds_IntMap();
		$this->map_b2a = new haxe_ds_IntMap();
		$this->ha = $this->hb = 0;
		$this->map_count = 0;
		$this->reference = null;
		$this->meta = null;
		$this->comp = null;
		$this->order_cache_has_reference = false;
		$this->ia = -1;
		$this->ib = -1;
		$this->marked_as_identical = false;
	}}
	public $map_a2b;
	public $map_b2a;
	public $ha;
	public $hb;
	public $ta;
	public $tb;
	public $ia;
	public $ib;
	public $map_count;
	public $order_cache;
	public $order_cache_has_reference;
	public $index_columns;
	public $marked_as_identical;
	public $reference;
	public $meta;
	public $comp;
	public $has_addition;
	public $has_removal;
	public function range($ha, $hb) {
		$this->ha = $ha;
		$this->hb = $hb;
	}
	public function tables($ta, $tb) {
		$this->ta = $ta;
		$this->tb = $tb;
	}
	public function headers($ia, $ib) {
		$this->ia = $ia;
		$this->ib = $ib;
	}
	public function setRowlike($flag) {}
	public function link($a, $b) {
		if($a !== -1) {
			$this->map_a2b->set($a, $b);
		} else {
			$this->has_addition = true;
		}
		if($b !== -1) {
			$this->map_b2a->set($b, $a);
		} else {
			$this->has_removal = true;
		}
		$this->map_count++;
	}
	public function addIndexColumns($unit) {
		if($this->index_columns === null) {
			$this->index_columns = new _hx_array(array());
		}
		$this->index_columns->push($unit);
	}
	public function getIndexColumns() {
		return $this->index_columns;
	}
	public function a2b($a) {
		return $this->map_a2b->get($a);
	}
	public function b2a($b) {
		return $this->map_b2a->get($b);
	}
	public function count() {
		return $this->map_count;
	}
	public function toString() {
		$result = "" . _hx_string_or_null($this->map_a2b->toString()) . " // " . _hx_string_or_null($this->map_b2a->toString());
		if($this->reference !== null) {
			$result .= " (" . Std::string($this->reference) . ")";
		}
		return $result;
	}
	public function toOrder() {
		if($this->order_cache !== null) {
			if($this->reference !== null) {
				if(!$this->order_cache_has_reference) {
					$this->order_cache = null;
				}
			}
		}
		if($this->order_cache === null) {
			$this->order_cache = $this->toOrder3();
		}
		if($this->reference !== null) {
			$this->order_cache_has_reference = true;
		}
		return $this->order_cache;
	}
	public function addToOrder($l, $r, $p = null) {
		if($p === null) {
			$p = -2;
		}
		if($this->order_cache === null) {
			$this->order_cache = new coopy_Ordering();
		}
		$this->order_cache->add($l, $r, $p);
		$this->order_cache_has_reference = $p !== -2;
	}
	public function getSource() {
		return $this->ta;
	}
	public function getTarget() {
		return $this->tb;
	}
	public function getSourceHeader() {
		return $this->ia;
	}
	public function getTargetHeader() {
		return $this->ib;
	}
	public function toOrder3() {
		$order = new _hx_array(array());
		if($this->reference === null) {
			if(null == $this->map_a2b) throw new HException('null iterable');
			$__hx__it = $this->map_a2b->keys();
			while($__hx__it->hasNext()) {
				unset($k);
				$k = $__hx__it->next();
				$unit = new coopy_Unit(null, null, null);
				$unit->l = $k;
				$unit->r = $this->a2b($k);
				$order->push($unit);
				unset($unit);
			}
			if(null == $this->map_b2a) throw new HException('null iterable');
			$__hx__it = $this->map_b2a->keys();
			while($__hx__it->hasNext()) {
				unset($k1);
				$k1 = $__hx__it->next();
				if($this->b2a($k1) === -1) {
					$unit1 = new coopy_Unit(null, null, null);
					$unit1->l = -1;
					$unit1->r = $k1;
					$order->push($unit1);
					unset($unit1);
				}
			}
		} else {
			if(null == $this->map_a2b) throw new HException('null iterable');
			$__hx__it = $this->map_a2b->keys();
			while($__hx__it->hasNext()) {
				unset($k2);
				$k2 = $__hx__it->next();
				$unit2 = new coopy_Unit(null, null, null);
				$unit2->p = $k2;
				$unit2->l = $this->reference->a2b($k2);
				$unit2->r = $this->a2b($k2);
				$order->push($unit2);
				unset($unit2);
			}
			if(null == $this->reference->map_b2a) throw new HException('null iterable');
			$__hx__it = $this->reference->map_b2a->keys();
			while($__hx__it->hasNext()) {
				unset($k3);
				$k3 = $__hx__it->next();
				if($this->reference->b2a($k3) === -1) {
					$unit3 = new coopy_Unit(null, null, null);
					$unit3->p = -1;
					$unit3->l = $k3;
					$unit3->r = -1;
					$order->push($unit3);
					unset($unit3);
				}
			}
			if(null == $this->map_b2a) throw new HException('null iterable');
			$__hx__it = $this->map_b2a->keys();
			while($__hx__it->hasNext()) {
				unset($k4);
				$k4 = $__hx__it->next();
				if($this->b2a($k4) === -1) {
					$unit4 = new coopy_Unit(null, null, null);
					$unit4->p = -1;
					$unit4->l = -1;
					$unit4->r = $k4;
					$order->push($unit4);
					unset($unit4);
				}
			}
		}
		$top = $order->length;
		$remotes = new _hx_array(array());
		$locals = new _hx_array(array());
		{
			$_g = 0;
			while($_g < $top) {
				$o = $_g++;
				if(_hx_array_get($order, $o)->r >= 0) {
					$remotes->push($o);
				} else {
					$locals->push($o);
				}
				unset($o);
			}
		}
		$remote_sort = array(new _hx_lambda(array(&$locals, &$order, &$remotes, &$top), "coopy_Alignment_0"), 'execute');
		$local_sort = array(new _hx_lambda(array(&$locals, &$order, &$remote_sort, &$remotes, &$top), "coopy_Alignment_1"), 'execute');
		if($this->reference !== null) {
			$remote_sort = array(new _hx_lambda(array(&$local_sort, &$locals, &$order, &$remote_sort, &$remotes, &$top), "coopy_Alignment_2"), 'execute');
			$local_sort = array(new _hx_lambda(array(&$local_sort, &$locals, &$order, &$remote_sort, &$remotes, &$top), "coopy_Alignment_3"), 'execute');
		}
		$remotes->sort($remote_sort);
		$locals->sort($local_sort);
		$revised_order = new _hx_array(array());
		$at_r = 0;
		$at_l = 0;
		{
			$_g1 = 0;
			while($_g1 < $top) {
				$o4 = $_g1++;
				if($at_r < $remotes->length && $at_l < $locals->length) {
					$ur = $order[$remotes[$at_r]];
					$ul = $order[$locals[$at_l]];
					if($ul->l === -1 && $ul->p >= 0 && $ur->p >= 0) {
						if($ur->p > $ul->p) {
							$revised_order->push($ul);
							$at_l++;
							continue;
						}
					} else {
						if($ur->l > $ul->l) {
							$revised_order->push($ul);
							$at_l++;
							continue;
						}
					}
					$revised_order->push($ur);
					$at_r++;
					continue;
					unset($ur,$ul);
				}
				if($at_r < $remotes->length) {
					$ur1 = $order[$remotes[$at_r]];
					$revised_order->push($ur1);
					$at_r++;
					continue;
					unset($ur1);
				}
				if($at_l < $locals->length) {
					$ul1 = $order[$locals[$at_l]];
					$revised_order->push($ul1);
					$at_l++;
					continue;
					unset($ul1);
				}
				unset($o4);
			}
		}
		$order = $revised_order;
		$result = new coopy_Ordering();
		$result->setList($order);
		if($this->reference === null) {
			$result->ignoreParent();
		}
		return $result;
	}
	public function markIdentical() {
		$this->marked_as_identical = true;
	}
	public function isMarkedAsIdentical() {
		return $this->marked_as_identical;
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
	function __toString() { return $this->toString(); }
}
function coopy_Alignment_0(&$locals, &$order, &$remotes, &$top, $a, $b) {
	{
		return _hx_array_get($order, $a)->r - _hx_array_get($order, $b)->r;
	}
}
function coopy_Alignment_1(&$locals, &$order, &$remote_sort, &$remotes, &$top, $a1, $b1) {
	{
		if($a1 === $b1) {
			return 0;
		}
		if(_hx_array_get($order, $a1)->l >= 0 && _hx_array_get($order, $b1)->l >= 0) {
			return _hx_array_get($order, $a1)->l - _hx_array_get($order, $b1)->l;
		}
		if(_hx_array_get($order, $a1)->l >= 0) {
			return 1;
		}
		if(_hx_array_get($order, $b1)->l >= 0) {
			return -1;
		}
		return $a1 - $b1;
	}
}
function coopy_Alignment_2(&$local_sort, &$locals, &$order, &$remote_sort, &$remotes, &$top, $a2, $b2) {
	{
		if($a2 === $b2) {
			return 0;
		}
		$o1 = _hx_array_get($order, $a2)->r - _hx_array_get($order, $b2)->r;
		if(_hx_array_get($order, $a2)->p >= 0 && _hx_array_get($order, $b2)->p >= 0) {
			$o2 = _hx_array_get($order, $a2)->p - _hx_array_get($order, $b2)->p;
			if($o1 * $o2 < 0) {
				return $o1;
			}
			$o3 = _hx_array_get($order, $a2)->l - _hx_array_get($order, $b2)->l;
			return $o3;
		}
		return $o1;
	}
}
function coopy_Alignment_3(&$local_sort, &$locals, &$order, &$remote_sort, &$remotes, &$top, $a3, $b3) {
	{
		if($a3 === $b3) {
			return 0;
		}
		if(_hx_array_get($order, $a3)->l >= 0 && _hx_array_get($order, $b3)->l >= 0) {
			$o11 = _hx_array_get($order, $a3)->l - _hx_array_get($order, $b3)->l;
			if(_hx_array_get($order, $a3)->p >= 0 && _hx_array_get($order, $b3)->p >= 0) {
				$o21 = _hx_array_get($order, $a3)->p - _hx_array_get($order, $b3)->p;
				if($o11 * $o21 < 0) {
					return $o11;
				}
				return $o21;
			}
		}
		if(_hx_array_get($order, $a3)->l >= 0) {
			return 1;
		}
		if(_hx_array_get($order, $b3)->l >= 0) {
			return -1;
		}
		return $a3 - $b3;
	}
}
