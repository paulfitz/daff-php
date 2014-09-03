<?php

class coopy_Alignment {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.Alignment::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->map_a2b = new haxe_ds_IntMap();
		$this->map_b2a = new haxe_ds_IntMap();
		$this->ha = $this->hb = 0;
		$this->map_count = 0;
		$this->reference = null;
		$this->meta = null;
		$this->order_cache_has_reference = false;
		$this->ia = 0;
		$this->ib = 0;
		$GLOBALS['%s']->pop();
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
		$GLOBALS['%s']->push("coopy.Alignment::range");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ha = $ha;
		$this->hb = $hb;
		$GLOBALS['%s']->pop();
	}
	public function tables($ta, $tb) {
		$GLOBALS['%s']->push("coopy.Alignment::tables");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ta = $ta;
		$this->tb = $tb;
		$GLOBALS['%s']->pop();
	}
	public function headers($ia, $ib) {
		$GLOBALS['%s']->push("coopy.Alignment::headers");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->ia = $ia;
		$this->ib = $ib;
		$GLOBALS['%s']->pop();
	}
	public function setRowlike($flag) {
		$GLOBALS['%s']->push("coopy.Alignment::setRowlike");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}
	public function link($a, $b) {
		$GLOBALS['%s']->push("coopy.Alignment::link");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->map_a2b->set($a, $b);
		$this->map_b2a->set($b, $a);
		$this->map_count++;
		$GLOBALS['%s']->pop();
	}
	public function addIndexColumns($unit) {
		$GLOBALS['%s']->push("coopy.Alignment::addIndexColumns");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->index_columns === null) {
			$this->index_columns = new _hx_array(array());
		}
		$this->index_columns->push($unit);
		$GLOBALS['%s']->pop();
	}
	public function getIndexColumns() {
		$GLOBALS['%s']->push("coopy.Alignment::getIndexColumns");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->index_columns;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function a2b($a) {
		$GLOBALS['%s']->push("coopy.Alignment::a2b");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->map_a2b->get($a);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function b2a($b) {
		$GLOBALS['%s']->push("coopy.Alignment::b2a");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->map_b2a->get($b);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function count() {
		$GLOBALS['%s']->push("coopy.Alignment::count");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->map_count;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toString() {
		$GLOBALS['%s']->push("coopy.Alignment::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = "" . _hx_string_or_null($this->map_a2b->toString());
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toOrderPruned($rowlike) {
		$GLOBALS['%s']->push("coopy.Alignment::toOrderPruned");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->toOrderCached(true, $rowlike);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toOrder() {
		$GLOBALS['%s']->push("coopy.Alignment::toOrder");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->toOrderCached(false, false);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getSource() {
		$GLOBALS['%s']->push("coopy.Alignment::getSource");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->ta;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getTarget() {
		$GLOBALS['%s']->push("coopy.Alignment::getTarget");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->tb;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getSourceHeader() {
		$GLOBALS['%s']->push("coopy.Alignment::getSourceHeader");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->ia;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getTargetHeader() {
		$GLOBALS['%s']->push("coopy.Alignment::getTargetHeader");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->ib;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toOrderCached($prune, $rowlike) {
		$GLOBALS['%s']->push("coopy.Alignment::toOrderCached");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->order_cache !== null) {
			if($this->reference !== null) {
				if(!$this->order_cache_has_reference) {
					$this->order_cache = null;
				}
			}
		}
		if($this->order_cache === null) {
			$this->order_cache = $this->toOrder3($prune, $rowlike);
		}
		if($this->reference !== null) {
			$this->order_cache_has_reference = true;
		}
		{
			$tmp = $this->order_cache;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function pruneOrder($o, $ref, $rowlike) {
		$GLOBALS['%s']->push("coopy.Alignment::pruneOrder");
		$__hx__spos = $GLOBALS['%s']->length;
		$tl = $ref->tb;
		$tr = $this->tb;
		if($rowlike) {
			if($tl->get_width() !== $tr->get_width()) {
				$GLOBALS['%s']->pop();
				return;
			}
		} else {
			if($tl->get_height() !== $tr->get_height()) {
				$GLOBALS['%s']->pop();
				return;
			}
		}
		$units = $o->getList();
		$left_units = new _hx_array(array());
		$left_locs = new _hx_array(array());
		$right_units = new _hx_array(array());
		$right_locs = new _hx_array(array());
		$eliminate = new _hx_array(array());
		$ct = 0;
		{
			$_g1 = 0;
			$_g = $units->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$unit = $units[$i];
				if($unit->l < 0 && $unit->r >= 0) {
					$right_units->push($unit);
					$right_locs->push($i);
					$ct++;
				} else {
					if($unit->r < 0 && $unit->l >= 0) {
						$left_units->push($unit);
						$left_locs->push($i);
						$ct++;
					} else {
						if($ct > 0) {
							$left_units->splice(0, $left_units->length);
							$right_units->splice(0, $right_units->length);
							$left_locs->splice(0, $left_locs->length);
							$right_locs->splice(0, $right_locs->length);
							$ct = 0;
						}
					}
				}
				while($left_locs->length > 0 && $right_locs->length > 0) {
					$l = _hx_array_get($left_units, 0)->l;
					$r = _hx_array_get($right_units, 0)->r;
					$view = $tl->getCellView();
					$match = true;
					if($rowlike) {
						$w = $tl->get_width();
						{
							$_g2 = 0;
							while($_g2 < $w) {
								$j = $_g2++;
								if(!$view->equals($tl->getCell($j, $l), $tr->getCell($j, $r))) {
									$match = false;
									break;
								}
								unset($j);
							}
							unset($_g2);
						}
						unset($w);
					} else {
						$h = $tl->get_height();
						{
							$_g21 = 0;
							while($_g21 < $h) {
								$j1 = $_g21++;
								if(!$view->equals($tl->getCell($l, $j1), $tr->getCell($r, $j1))) {
									$match = false;
									break;
								}
								unset($j1);
							}
							unset($_g21);
						}
						unset($h);
					}
					if($match) {
						$eliminate->push($left_locs[0]);
						$eliminate->push($right_locs[0]);
					}
					$left_units->shift();
					$right_units->shift();
					$left_locs->shift();
					$right_locs->shift();
					$ct -= 2;
					unset($view,$r,$match,$l);
				}
				unset($unit,$i);
			}
		}
		if($eliminate->length > 0) {
			$eliminate->sort(array(new _hx_lambda(array(&$ct, &$eliminate, &$left_locs, &$left_units, &$o, &$ref, &$right_locs, &$right_units, &$rowlike, &$tl, &$tr, &$units), "coopy_Alignment_0"), 'execute'));
			$del = 0;
			{
				$_g3 = 0;
				while($_g3 < $eliminate->length) {
					$e = $eliminate[$_g3];
					++$_g3;
					$o->getList()->splice($e - $del, 1);
					$del++;
					unset($e);
				}
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function toOrder3($prune, $rowlike) {
		$GLOBALS['%s']->push("coopy.Alignment::toOrder3");
		$__hx__spos = $GLOBALS['%s']->length;
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
				haxe_Log::trace("Ordering took too long, something went wrong", _hx_anonymous(array("fileName" => "Alignment.hx", "lineNumber" => 241, "className" => "coopy.Alignment", "methodName" => "toOrder3")));
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
		if($prune) {
			$this->pruneOrder($order, $ref, $rowlike);
		}
		{
			$GLOBALS['%s']->pop();
			return $order;
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
	function __toString() { return $this->toString(); }
}
function coopy_Alignment_0(&$ct, &$eliminate, &$left_locs, &$left_units, &$o, &$ref, &$right_locs, &$right_units, &$rowlike, &$tl, &$tr, &$units, $a, $b) {
	{
		$GLOBALS['%s']->push("coopy.Alignment::pruneOrder@195");
		$__hx__spos2 = $GLOBALS['%s']->length;
		{
			$tmp = $a - $b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
}
