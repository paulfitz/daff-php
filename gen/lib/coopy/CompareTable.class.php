<?php

class coopy_CompareTable {
	public function __construct($comp) {
		if(!php_Boot::$skip_constructor) {
		$this->comp = $comp;
		if($comp->compare_flags !== null) {
			if($comp->compare_flags->parent !== null) {
				$comp->p = $comp->compare_flags->parent;
			}
		}
	}}
	public $comp;
	public $indexes;
	public function run() {
		if($this->useSql()) {
			$this->comp->completed = true;
			return false;
		}
		$more = $this->compareCore();
		while($more && $this->comp->run_to_completion) {
			$more = $this->compareCore();
		}
		return !$more;
	}
	public function align() {
		while(!$this->comp->completed) {
			$this->run();
		}
		$alignment = new coopy_Alignment();
		$this->alignCore($alignment);
		$alignment->comp = $this->comp;
		$this->comp->alignment = $alignment;
		return $alignment;
	}
	public function getComparisonState() {
		return $this->comp;
	}
	public function alignCore($align) {
		if($this->useSql()) {
			$tab1 = null;
			$tab2 = null;
			$tab3 = null;
			if($this->comp->p === null) {
				$tab1 = $this->comp->a;
				$tab2 = $this->comp->b;
			} else {
				$align->reference = new coopy_Alignment();
				$tab1 = $this->comp->p;
				$tab2 = $this->comp->b;
				$tab3 = $this->comp->a;
			}
			$db = null;
			if($tab1 !== null) {
				$db = $tab1->getDatabase();
			}
			if($db === null && $tab2 !== null) {
				$db = $tab2->getDatabase();
			}
			if($db === null && $tab3 !== null) {
				$db = $tab3->getDatabase();
			}
			$sc = new coopy_SqlCompare($db, $tab1, $tab2, $tab3, $align);
			$sc->apply();
			if($this->comp->p !== null) {
				$align->meta->reference = $align->reference->meta;
			}
			return;
		}
		if($this->comp->p === null) {
			$this->alignCore2($align, $this->comp->a, $this->comp->b);
			return;
		}
		$align->reference = new coopy_Alignment();
		$this->alignCore2($align, $this->comp->p, $this->comp->b);
		$this->alignCore2($align->reference, $this->comp->p, $this->comp->a);
		$align->meta->reference = $align->reference->meta;
	}
	public function alignCore2($align, $a, $b) {
		if($align->meta === null) {
			$align->meta = new coopy_Alignment();
		}
		$this->alignColumns($align->meta, $a, $b);
		$column_order = $align->meta->toOrder();
		$align->range($a->get_height(), $b->get_height());
		$align->tables($a, $b);
		$align->setRowlike(true);
		$w = $a->get_width();
		$ha = $a->get_height();
		$hb = $b->get_height();
		$av = $a->getCellView();
		$ids = null;
		$ignore = null;
		if($this->comp->compare_flags !== null) {
			$ids = $this->comp->compare_flags->ids;
			$ignore = $this->comp->compare_flags->getIgnoredColumns();
		}
		$common_units = new _hx_array(array());
		$ra_header = $align->getSourceHeader();
		$rb_header = $align->getSourceHeader();
		{
			$_g = 0;
			$_g1 = $column_order->getList();
			while($_g < $_g1->length) {
				$unit = $_g1[$_g];
				++$_g;
				if($unit->l >= 0 && $unit->r >= 0 && $unit->p !== -1) {
					if($ignore !== null) {
						if($unit->l >= 0 && $ra_header >= 0 && $ra_header < $a->get_height()) {
							$name = $av->toString($a->getCell($unit->l, $ra_header));
							if($ignore->exists($name)) {
								continue;
							}
							unset($name);
						}
						if($unit->r >= 0 && $rb_header >= 0 && $rb_header < $b->get_height()) {
							$name1 = $av->toString($b->getCell($unit->r, $rb_header));
							if($ignore->exists($name1)) {
								continue;
							}
							unset($name1);
						}
					}
					$common_units->push($unit);
				}
				unset($unit);
			}
		}
		$index_top = null;
		$pending_ct = $ha;
		$reverse_pending_ct = $hb;
		$used = new haxe_ds_IntMap();
		$used_reverse = new haxe_ds_IntMap();
		if($ids !== null) {
			$index_top = new coopy_IndexPair($this->comp->compare_flags);
			$ids_as_map = new haxe_ds_StringMap();
			{
				$_g2 = 0;
				while($_g2 < $ids->length) {
					$id = $ids[$_g2];
					++$_g2;
					{
						$ids_as_map->set($id, true);
						true;
					}
					unset($id);
				}
			}
			{
				$_g3 = 0;
				while($_g3 < $common_units->length) {
					$unit1 = $common_units[$_g3];
					++$_g3;
					$na = $av->toString($a->getCell($unit1->l, 0));
					$nb = $av->toString($b->getCell($unit1->r, 0));
					if($ids_as_map->exists($na) || $ids_as_map->exists($nb)) {
						$index_top->addColumns($unit1->l, $unit1->r);
						$align->addIndexColumns($unit1);
					}
					unset($unit1,$nb,$na);
				}
			}
			$index_top->indexTables($a, $b, 1);
			if($this->indexes !== null) {
				$this->indexes->push($index_top);
			}
			{
				$_g4 = 0;
				while($_g4 < $ha) {
					$j = $_g4++;
					$cross = $index_top->queryLocal($j);
					$spot_a = $cross->spot_a;
					$spot_b = $cross->spot_b;
					if($spot_a !== 1 || $spot_b !== 1) {
						continue;
					}
					$jb = $cross->item_b->lst[0];
					$align->link($j, $jb);
					$used->set($jb, 1);
					if(!$used_reverse->exists($j)) {
						$reverse_pending_ct--;
					}
					$used_reverse->set($j, 1);
					unset($spot_b,$spot_a,$jb,$j,$cross);
				}
			}
		} else {
			$N = 5;
			$columns = new _hx_array(array());
			if($common_units->length > $N) {
				$columns_eval = new _hx_array(array());
				{
					$_g11 = 0;
					$_g5 = $common_units->length;
					while($_g11 < $_g5) {
						$i = $_g11++;
						$ct = 0;
						$mem = new haxe_ds_StringMap();
						$mem2 = new haxe_ds_StringMap();
						$ca = _hx_array_get($common_units, $i)->l;
						$cb = _hx_array_get($common_units, $i)->r;
						{
							$_g21 = 0;
							while($_g21 < $ha) {
								$j1 = $_g21++;
								$key = $av->toString($a->getCell($ca, $j1));
								if(!$mem->exists($key)) {
									$mem->set($key, 1);
									$ct++;
								}
								unset($key,$j1);
							}
							unset($_g21);
						}
						{
							$_g22 = 0;
							while($_g22 < $hb) {
								$j2 = $_g22++;
								$key1 = $av->toString($b->getCell($cb, $j2));
								if(!$mem2->exists($key1)) {
									$mem2->set($key1, 1);
									$ct++;
								}
								unset($key1,$j2);
							}
							unset($_g22);
						}
						$columns_eval->push((new _hx_array(array($i, $ct))));
						unset($mem2,$mem,$i,$ct,$cb,$ca);
					}
				}
				$sorter = array(new _hx_lambda(array(&$N, &$a, &$align, &$av, &$b, &$column_order, &$columns, &$columns_eval, &$common_units, &$ha, &$hb, &$ids, &$ignore, &$index_top, &$pending_ct, &$ra_header, &$rb_header, &$reverse_pending_ct, &$used, &$used_reverse, &$w), "coopy_CompareTable_0"), 'execute');
				$columns_eval->sort($sorter);
				$columns = Lambda::harray(Lambda::map($columns_eval, array(new _hx_lambda(array(&$N, &$a, &$align, &$av, &$b, &$column_order, &$columns, &$columns_eval, &$common_units, &$ha, &$hb, &$ids, &$ignore, &$index_top, &$pending_ct, &$ra_header, &$rb_header, &$reverse_pending_ct, &$sorter, &$used, &$used_reverse, &$w), "coopy_CompareTable_1"), 'execute')));
				$columns = $columns->slice(0, $N);
			} else {
				$_g12 = 0;
				$_g6 = $common_units->length;
				while($_g12 < $_g6) {
					$i1 = $_g12++;
					$columns->push($i1);
					unset($i1);
				}
			}
			$top = Math::round(Math::pow(2, $columns->length));
			$pending = new haxe_ds_IntMap();
			{
				$_g7 = 0;
				while($_g7 < $ha) {
					$j3 = $_g7++;
					$pending->set($j3, $j3);
					unset($j3);
				}
			}
			$added_columns = new haxe_ds_IntMap();
			$index_ct = 0;
			{
				$_g8 = 0;
				while($_g8 < $top) {
					$k = $_g8++;
					if($k === 0) {
						continue;
					}
					if($pending_ct === 0) {
						break;
					}
					$active_columns = new _hx_array(array());
					$kk = $k;
					$at = 0;
					while($kk > 0) {
						if(_hx_mod($kk, 2) === 1) {
							$active_columns->push($columns[$at]);
						}
						$kk >>= 1;
						$at++;
					}
					$index = new coopy_IndexPair($this->comp->compare_flags);
					{
						$_g23 = 0;
						$_g13 = $active_columns->length;
						while($_g23 < $_g13) {
							$k1 = $_g23++;
							$col = $active_columns[$k1];
							$unit2 = $common_units[$col];
							$index->addColumns($unit2->l, $unit2->r);
							if(!$added_columns->exists($col)) {
								$align->addIndexColumns($unit2);
								$added_columns->set($col, true);
							}
							unset($unit2,$k1,$col);
						}
						unset($_g23,$_g13);
					}
					$index->indexTables($a, $b, 1);
					if($k === $top - 1) {
						$index_top = $index;
					}
					$h = $a->get_height();
					if($b->get_height() > $h) {
						$h = $b->get_height();
					}
					if($h < 1) {
						$h = 1;
					}
					$wide_top_freq = $index->getTopFreq();
					$ratio = $wide_top_freq;
					$ratio /= $h + 20;
					if($ratio >= 0.1) {
						if($index_ct > 0 || $k < $top - 1) {
							continue;
						}
					}
					$index_ct++;
					if($this->indexes !== null) {
						$this->indexes->push($index);
					}
					$fixed = new _hx_array(array());
					if(null == $pending) throw new HException('null iterable');
					$__hx__it = $pending->keys();
					while($__hx__it->hasNext()) {
						unset($j4);
						$j4 = $__hx__it->next();
						$cross1 = $index->queryLocal($j4);
						$spot_a1 = $cross1->spot_a;
						$spot_b1 = $cross1->spot_b;
						if($spot_a1 !== 1 || $spot_b1 !== 1) {
							continue;
						}
						$val = $cross1->item_b->lst[0];
						if(!$used->exists($val)) {
							$fixed->push($j4);
							$align->link($j4, $val);
							$used->set($val, 1);
							if(!$used_reverse->exists($j4)) {
								$reverse_pending_ct--;
							}
							$used_reverse->set($j4, 1);
						}
						unset($val,$spot_b1,$spot_a1,$cross1);
					}
					{
						$_g24 = 0;
						$_g14 = $fixed->length;
						while($_g24 < $_g14) {
							$j5 = $_g24++;
							$pending->remove($fixed[$j5]);
							$pending_ct--;
							unset($j5);
						}
						unset($_g24,$_g14);
					}
					unset($wide_top_freq,$ratio,$kk,$k,$index,$h,$fixed,$at,$active_columns);
				}
			}
		}
		if($index_top !== null) {
			$offset = 0;
			$scale = 1;
			{
				$_g9 = 0;
				while($_g9 < 2) {
					$sgn = $_g9++;
					if($pending_ct > 0) {
						$xb = null;
						if($scale === -1 && $hb > 0) {
							$xb = $hb - 1;
						}
						{
							$_g15 = 0;
							while($_g15 < $ha) {
								$xa0 = $_g15++;
								$xa = $xa0 * $scale + $offset;
								$xb2 = $align->a2b($xa);
								if($xb2 !== null) {
									$xb = $xb2 + $scale;
									if($xb >= $hb || $xb < 0) {
										break;
									}
									continue;
								}
								if($xb === null) {
									continue;
								}
								$ka = $index_top->localKey($xa);
								$kb = $index_top->remoteKey($xb);
								if($ka !== $kb) {
									continue;
								}
								if($used->exists($xb)) {
									continue;
								}
								$align->link($xa, $xb);
								$used->set($xb, 1);
								$used_reverse->set($xa, 1);
								$pending_ct--;
								$xb += $scale;
								if($xb >= $hb || $xb < 0) {
									break;
								}
								if($pending_ct === 0) {
									break;
								}
								unset($xb2,$xa0,$xa,$kb,$ka);
							}
							unset($_g15);
						}
						unset($xb);
					}
					$offset = $ha - 1;
					$scale = -1;
					unset($sgn);
				}
			}
			$offset = 0;
			$scale = 1;
			{
				$_g10 = 0;
				while($_g10 < 2) {
					$sgn1 = $_g10++;
					if($reverse_pending_ct > 0) {
						$xa1 = null;
						if($scale === -1 && $ha > 0) {
							$xa1 = $ha - 1;
						}
						{
							$_g16 = 0;
							while($_g16 < $hb) {
								$xb0 = $_g16++;
								$xb1 = $xb0 * $scale + $offset;
								$xa2 = $align->b2a($xb1);
								if($xa2 !== null) {
									$xa1 = $xa2 + $scale;
									if($xa1 >= $ha || $xa1 < 0) {
										break;
									}
									continue;
								}
								if($xa1 === null) {
									continue;
								}
								$ka1 = $index_top->localKey($xa1);
								$kb1 = $index_top->remoteKey($xb1);
								if($ka1 !== $kb1) {
									continue;
								}
								if($used_reverse->exists($xa1)) {
									continue;
								}
								$align->link($xa1, $xb1);
								$used->set($xb1, 1);
								$used_reverse->set($xa1, 1);
								$reverse_pending_ct--;
								$xa1 += $scale;
								if($xa1 >= $ha || $xa1 < 0) {
									break;
								}
								if($reverse_pending_ct === 0) {
									break;
								}
								unset($xb1,$xb0,$xa2,$kb1,$ka1);
							}
							unset($_g16);
						}
						unset($xa1);
					}
					$offset = $hb - 1;
					$scale = -1;
					unset($sgn1);
				}
			}
		}
		{
			$_g17 = 1;
			while($_g17 < $ha) {
				$i2 = $_g17++;
				if(!$used_reverse->exists($i2)) {
					$align->link($i2, -1);
				}
				unset($i2);
			}
		}
		{
			$_g18 = 1;
			while($_g18 < $hb) {
				$i3 = $_g18++;
				if(!$used->exists($i3)) {
					$align->link(-1, $i3);
				}
				unset($i3);
			}
		}
		if($ha > 0 && $hb > 0) {
			$align->link(0, 0);
		}
	}
	public function alignColumns($align, $a, $b) {
		$align->range($a->get_width(), $b->get_width());
		$align->tables($a, $b);
		$align->setRowlike(false);
		$slop = 5;
		$va = $a->getCellView();
		$vb = $b->getCellView();
		$ra_best = 0;
		$rb_best = 0;
		$ct_best = -1;
		$ma_best = null;
		$mb_best = null;
		$ra_header = 0;
		$rb_header = 0;
		$ra_uniques = 0;
		$rb_uniques = 0;
		{
			$_g = 0;
			while($_g < $slop) {
				$ra = $_g++;
				{
					$_g1 = 0;
					while($_g1 < $slop) {
						$rb = $_g1++;
						$ma = new haxe_ds_StringMap();
						$mb = new haxe_ds_StringMap();
						$ct = 0;
						$uniques = 0;
						if($ra < $a->get_height()) {
							{
								$_g3 = 0;
								$_g2 = $a->get_width();
								while($_g3 < $_g2) {
									$ca = $_g3++;
									$key = $va->toString($a->getCell($ca, $ra));
									if($ma->exists($key)) {
										$ma->set($key, -1);
										$uniques--;
									} else {
										$ma->set($key, $ca);
										$uniques++;
									}
									unset($key,$ca);
								}
								unset($_g3,$_g2);
							}
							if($uniques > $ra_uniques) {
								$ra_header = $ra;
								$ra_uniques = $uniques;
							}
						}
						$uniques = 0;
						if($rb < $b->get_height()) {
							{
								$_g31 = 0;
								$_g21 = $b->get_width();
								while($_g31 < $_g21) {
									$cb = $_g31++;
									$key1 = $vb->toString($b->getCell($cb, $rb));
									if($mb->exists($key1)) {
										$mb->set($key1, -1);
										$uniques--;
									} else {
										$mb->set($key1, $cb);
										$uniques++;
									}
									unset($key1,$cb);
								}
								unset($_g31,$_g21);
							}
							if($uniques > $rb_uniques) {
								$rb_header = $rb;
								$rb_uniques = $uniques;
							}
						}
						if(null == $ma) throw new HException('null iterable');
						$__hx__it = $ma->keys();
						while($__hx__it->hasNext()) {
							unset($key2);
							$key2 = $__hx__it->next();
							$i0 = $ma->get($key2);
							$i1 = $mb->get($key2);
							if($i1 !== null) {
								if($i1 >= 0 && $i0 >= 0) {
									$ct++;
								}
							}
							unset($i1,$i0);
						}
						if($ct > $ct_best) {
							$ct_best = $ct;
							$ma_best = $ma;
							$mb_best = $mb;
							$ra_best = $ra;
							$rb_best = $rb;
						}
						unset($uniques,$rb,$mb,$ma,$ct);
					}
					unset($_g1);
				}
				unset($ra);
			}
		}
		if($ma_best === null) {
			if($a->get_height() > 0 && $b->get_height() === 0) {
				$align->headers(0, -1);
			} else {
				if($a->get_height() === 0 && $b->get_height() > 0) {
					$align->headers(-1, 0);
				}
			}
			return;
		}
		if(null == $ma_best) throw new HException('null iterable');
		$__hx__it = $ma_best->keys();
		while($__hx__it->hasNext()) {
			unset($key3);
			$key3 = $__hx__it->next();
			$i01 = $ma_best->get($key3);
			$i11 = $mb_best->get($key3);
			if($i01 !== null && $i11 !== null) {
				$align->link($i01, $i11);
			} else {
				if($i01 !== null) {
					$align->link($i01, -1);
				} else {
					if($i11 !== null) {
						$align->link(-1, $i11);
					}
				}
			}
			unset($i11,$i01);
		}
		if(null == $mb_best) throw new HException('null iterable');
		$__hx__it = $mb_best->keys();
		while($__hx__it->hasNext()) {
			unset($key4);
			$key4 = $__hx__it->next();
			$i02 = $ma_best->get($key4);
			$i12 = $mb_best->get($key4);
			if($i02 === null && $i12 !== null) {
				$align->link(-1, $i12);
			}
			unset($i12,$i02);
		}
		$align->headers($ra_header, $rb_header);
	}
	public function testHasSameColumns() {
		$p = $this->comp->p;
		$a = $this->comp->a;
		$b = $this->comp->b;
		$eq = $this->hasSameColumns2($a, $b);
		if($eq && $p !== null) {
			$eq = $this->hasSameColumns2($p, $a);
		}
		$this->comp->has_same_columns = $eq;
		$this->comp->has_same_columns_known = true;
		return true;
	}
	public function hasSameColumns2($a, $b) {
		if($a->get_width() !== $b->get_width()) {
			return false;
		}
		if($a->get_height() === 0 || $b->get_height() === 0) {
			return true;
		}
		$av = $a->getCellView();
		{
			$_g1 = 0;
			$_g = $a->get_width();
			while($_g1 < $_g) {
				$i = $_g1++;
				{
					$_g3 = $i + 1;
					$_g2 = $a->get_width();
					while($_g3 < $_g2) {
						$j = $_g3++;
						if($av->equals($a->getCell($i, 0), $a->getCell($j, 0))) {
							return false;
						}
						unset($j);
					}
					unset($_g3,$_g2);
				}
				if(!$av->equals($a->getCell($i, 0), $b->getCell($i, 0))) {
					return false;
				}
				unset($i);
			}
		}
		return true;
	}
	public function testIsEqual() {
		$p = $this->comp->p;
		$a = $this->comp->a;
		$b = $this->comp->b;
		$this->comp->getMeta();
		$nested = false;
		if($this->comp->p_meta !== null) {
			if($this->comp->p_meta->isNested()) {
				$nested = true;
			}
		}
		if($this->comp->a_meta !== null) {
			if($this->comp->a_meta->isNested()) {
				$nested = true;
			}
		}
		if($this->comp->b_meta !== null) {
			if($this->comp->b_meta->isNested()) {
				$nested = true;
			}
		}
		if($nested) {
			$this->comp->is_equal = false;
			$this->comp->is_equal_known = true;
			return true;
		}
		$eq = $this->isEqual2($a, $b);
		if($eq && $p !== null) {
			$eq = $this->isEqual2($p, $a);
		}
		$this->comp->is_equal = $eq;
		$this->comp->is_equal_known = true;
		return true;
	}
	public function isEqual2($a, $b) {
		if($a->get_width() !== $b->get_width() || $a->get_height() !== $b->get_height()) {
			return false;
		}
		$av = $a->getCellView();
		{
			$_g1 = 0;
			$_g = $a->get_height();
			while($_g1 < $_g) {
				$i = $_g1++;
				{
					$_g3 = 0;
					$_g2 = $a->get_width();
					while($_g3 < $_g2) {
						$j = $_g3++;
						if(!$av->equals($a->getCell($j, $i), $b->getCell($j, $i))) {
							return false;
						}
						unset($j);
					}
					unset($_g3,$_g2);
				}
				unset($i);
			}
		}
		return true;
	}
	public function compareCore() {
		if($this->comp->completed) {
			return false;
		}
		if(!$this->comp->is_equal_known) {
			return $this->testIsEqual();
		}
		if(!$this->comp->has_same_columns_known) {
			return $this->testHasSameColumns();
		}
		$this->comp->completed = true;
		return false;
	}
	public function storeIndexes() {
		$this->indexes = new _hx_array(array());
	}
	public function getIndexes() {
		return $this->indexes;
	}
	public function useSql() {
		if($this->comp->compare_flags === null) {
			return false;
		}
		$this->comp->getMeta();
		$sql = true;
		if($this->comp->p_meta !== null) {
			if(!$this->comp->p_meta->isSql()) {
				$sql = false;
			}
		}
		if($this->comp->a_meta !== null) {
			if(!$this->comp->a_meta->isSql()) {
				$sql = false;
			}
		}
		if($this->comp->b_meta !== null) {
			if(!$this->comp->b_meta->isSql()) {
				$sql = false;
			}
		}
		if($this->comp->p !== null && $this->comp->p_meta === null) {
			$sql = false;
		}
		if($this->comp->a !== null && $this->comp->a_meta === null) {
			$sql = false;
		}
		if($this->comp->b !== null && $this->comp->b_meta === null) {
			$sql = false;
		}
		return $sql;
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
	function __toString() { return 'coopy.CompareTable'; }
}
function coopy_CompareTable_0(&$N, &$a, &$align, &$av, &$b, &$column_order, &$columns, &$columns_eval, &$common_units, &$ha, &$hb, &$ids, &$ignore, &$index_top, &$pending_ct, &$ra_header, &$rb_header, &$reverse_pending_ct, &$used, &$used_reverse, &$w, $a1, $b1) {
	{
		if($a1->a[1] < $b1[1]) {
			return 1;
		}
		if($a1->a[1] > $b1[1]) {
			return -1;
		}
		if($a1->a[0] > $b1[0]) {
			return 1;
		}
		if($a1->a[0] < $b1[0]) {
			return -1;
		}
		return 0;
	}
}
function coopy_CompareTable_1(&$N, &$a, &$align, &$av, &$b, &$column_order, &$columns, &$columns_eval, &$common_units, &$ha, &$hb, &$ids, &$ignore, &$index_top, &$pending_ct, &$ra_header, &$rb_header, &$reverse_pending_ct, &$sorter, &$used, &$used_reverse, &$w, $v) {
	{
		return $v[0];
	}
}
