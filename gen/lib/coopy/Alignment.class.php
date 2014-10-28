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
		$this->order_cache_has_reference = false;
		$this->ia = -1;
		$this->ib = -1;
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
	public $reference;
	public $meta;
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
		$this->map_a2b->set($a, $b);
		$this->map_b2a->set($b, $a);
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
		return "" . _hx_string_or_null($this->map_a2b->toString());
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
		$ref = $this->reference;
		if($ref === null) {
			$ref = new coopy_Alignment();
			$ref->range($this->ha, $this->ha);
			$ref->tables($this->ta, $this->ta);
			{
				$_g1 = 0;
				$_g = $this->ha;
				while($_g1 < $_g) {
					$i = $_g1++;
					$ref->link($i, $i);
					unset($i);
				}
			}
		}
		$order = new coopy_Ordering();
		if($this->reference === null) {
			$order->ignoreParent();
		}
		$xp = 0;
		$xl = 0;
		$xr = 0;
		$hp = $this->ha;
		$hl = $ref->hb;
		$hr = $this->hb;
		$vp = new haxe_ds_IntMap();
		$vl = new haxe_ds_IntMap();
		$vr = new haxe_ds_IntMap();
		{
			$_g2 = 0;
			while($_g2 < $hp) {
				$i1 = $_g2++;
				$vp->set($i1, $i1);
				unset($i1);
			}
		}
		{
			$_g3 = 0;
			while($_g3 < $hl) {
				$i2 = $_g3++;
				$vl->set($i2, $i2);
				unset($i2);
			}
		}
		{
			$_g4 = 0;
			while($_g4 < $hr) {
				$i3 = $_g4++;
				$vr->set($i3, $i3);
				unset($i3);
			}
		}
		$ct_vp = $hp;
		$ct_vl = $hl;
		$ct_vr = $hr;
		$prev = -1;
		$ct = 0;
		$max_ct = ($hp + $hl + $hr) * 10;
		while($ct_vp > 0 || $ct_vl > 0 || $ct_vr > 0) {
			$ct++;
			if($ct > $max_ct) {
				haxe_Log::trace("Ordering took too long, something went wrong", _hx_anonymous(array("fileName" => "Alignment.hx", "lineNumber" => 263, "className" => "coopy.Alignment", "methodName" => "toOrder3")));
				break;
			}
			if($xp >= $hp) {
				$xp = 0;
			}
			if($xl >= $hl) {
				$xl = 0;
			}
			if($xr >= $hr) {
				$xr = 0;
			}
			if($xp < $hp && $ct_vp > 0) {
				if($this->a2b($xp) === null && $ref->a2b($xp) === null) {
					if($vp->exists($xp)) {
						$order->add(-1, -1, $xp);
						$prev = $xp;
						$vp->remove($xp);
						$ct_vp--;
					}
					$xp++;
					continue;
				}
			}
			$zl = null;
			$zr = null;
			if($xl < $hl && $ct_vl > 0) {
				$zl = $ref->b2a($xl);
				if($zl === null) {
					if($vl->exists($xl)) {
						$order->add($xl, -1, -1);
						$vl->remove($xl);
						$ct_vl--;
					}
					$xl++;
					continue;
				}
			}
			if($xr < $hr && $ct_vr > 0) {
				$zr = $this->b2a($xr);
				if($zr === null) {
					if($vr->exists($xr)) {
						$order->add(-1, $xr, -1);
						$vr->remove($xr);
						$ct_vr--;
					}
					$xr++;
					continue;
				}
			}
			if($zl !== null) {
				if($this->a2b($zl) === null) {
					if($vl->exists($xl)) {
						$order->add($xl, -1, $zl);
						$prev = $zl;
						$vp->remove($zl);
						$ct_vp--;
						$vl->remove($xl);
						$ct_vl--;
						$xp = $zl + 1;
					}
					$xl++;
					continue;
				}
			}
			if($zr !== null) {
				if($ref->a2b($zr) === null) {
					if($vr->exists($xr)) {
						$order->add(-1, $xr, $zr);
						$prev = $zr;
						$vp->remove($zr);
						$ct_vp--;
						$vr->remove($xr);
						$ct_vr--;
						$xp = $zr + 1;
					}
					$xr++;
					continue;
				}
			}
			if($zl !== null && $zr !== null && $this->a2b($zl) !== null && $ref->a2b($zr) !== null) {
				if($zl === $prev + 1 || $zr !== $prev + 1) {
					if($vr->exists($xr)) {
						$order->add($ref->a2b($zr), $xr, $zr);
						$prev = $zr;
						$vp->remove($zr);
						$ct_vp--;
						{
							$key = $ref->a2b($zr);
							$vl->remove($key);
							unset($key);
						}
						$ct_vl--;
						$vr->remove($xr);
						$ct_vr--;
						$xp = $zr + 1;
						$xl = $ref->a2b($zr) + 1;
					}
					$xr++;
					continue;
				} else {
					if($vl->exists($xl)) {
						$order->add($xl, $this->a2b($zl), $zl);
						$prev = $zl;
						$vp->remove($zl);
						$ct_vp--;
						$vl->remove($xl);
						$ct_vl--;
						{
							$key1 = $this->a2b($zl);
							$vr->remove($key1);
							unset($key1);
						}
						$ct_vr--;
						$xp = $zl + 1;
						$xr = $this->a2b($zl) + 1;
					}
					$xl++;
					continue;
				}
			}
			$xp++;
			$xl++;
			$xr++;
			unset($zr,$zl);
		}
		return $order;
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
