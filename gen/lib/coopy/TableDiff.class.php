<?php

class coopy_TableDiff {
	public function __construct($align, $flags) {
		if(!php_Boot::$skip_constructor) {
		$this->align = $align;
		$this->flags = $flags;
		$this->builder = null;
	}}
	public $align;
	public $flags;
	public $builder;
	public function setCellBuilder($builder) {
		$this->builder = $builder;
	}
	public function getSeparator($t, $t2, $root) {
		$sep = $root;
		$w = $t->get_width();
		$h = $t->get_height();
		$view = $t->getCellView();
		{
			$_g = 0;
			while($_g < $h) {
				$y = $_g++;
				{
					$_g1 = 0;
					while($_g1 < $w) {
						$x = $_g1++;
						$txt = $view->toString($t->getCell($x, $y));
						if($txt === null) {
							continue;
						}
						while(_hx_index_of($txt, $sep, null) >= 0) {
							$sep = "-" . _hx_string_or_null($sep);
						}
						unset($x,$txt);
					}
					unset($_g1);
				}
				unset($y);
			}
		}
		if($t2 !== null) {
			$w = $t2->get_width();
			$h = $t2->get_height();
			{
				$_g2 = 0;
				while($_g2 < $h) {
					$y1 = $_g2++;
					{
						$_g11 = 0;
						while($_g11 < $w) {
							$x1 = $_g11++;
							$txt1 = $view->toString($t2->getCell($x1, $y1));
							if($txt1 === null) {
								continue;
							}
							while(_hx_index_of($txt1, $sep, null) >= 0) {
								$sep = "-" . _hx_string_or_null($sep);
							}
							unset($x1,$txt1);
						}
						unset($_g11);
					}
					unset($y1);
				}
			}
		}
		return $sep;
	}
	public function quoteForDiff($v, $d) {
		$nil = "NULL";
		if($v->equals($d, null)) {
			return $nil;
		}
		$str = $v->toString($d);
		$score = 0;
		{
			$_g1 = 0;
			$_g = strlen($str);
			while($_g1 < $_g) {
				$i = $_g1++;
				if(_hx_char_code_at($str, $score) !== 95) {
					break;
				}
				$score++;
				unset($i);
			}
		}
		if(_hx_substr($str, $score, null) === $nil) {
			$str = "_" . _hx_string_or_null($str);
		}
		return $str;
	}
	public function isReordered($m, $ct) {
		$reordered = false;
		$l = -1;
		$r = -1;
		{
			$_g = 0;
			while($_g < $ct) {
				$i = $_g++;
				$unit = $m->get($i);
				if($unit === null) {
					continue;
				}
				if($unit->l >= 0) {
					if($unit->l < $l) {
						$reordered = true;
						break;
					}
					$l = $unit->l;
				}
				if($unit->r >= 0) {
					if($unit->r < $r) {
						$reordered = true;
						break;
					}
					$r = $unit->r;
				}
				unset($unit,$i);
			}
		}
		return $reordered;
	}
	public function spreadContext($units, $del, $active) {
		if($del > 0 && $active !== null) {
			$mark = -$del - 1;
			$skips = 0;
			{
				$_g1 = 0;
				$_g = $units->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					if($active[$i] === -3) {
						$skips++;
						continue;
					}
					if($active[$i] === 0 || $active[$i] === 3) {
						if($i - $mark <= $del + $skips) {
							$active[$i] = 2;
						} else {
							if($i - $mark === $del + 1 + $skips) {
								$active[$i] = 3;
							}
						}
					} else {
						if($active[$i] === 1) {
							$mark = $i;
							$skips = 0;
						}
					}
					unset($i);
				}
			}
			$mark = $units->length + $del + 1;
			$skips = 0;
			{
				$_g11 = 0;
				$_g2 = $units->length;
				while($_g11 < $_g2) {
					$j = $_g11++;
					$i1 = $units->length - 1 - $j;
					if($active[$i1] === -3) {
						$skips++;
						continue;
					}
					if($active[$i1] === 0 || $active[$i1] === 3) {
						if($mark - $i1 <= $del + $skips) {
							$active[$i1] = 2;
						} else {
							if($mark - $i1 === $del + 1 + $skips) {
								$active[$i1] = 3;
							}
						}
					} else {
						if($active[$i1] === 1) {
							$mark = $i1;
							$skips = 0;
						}
					}
					unset($j,$i1);
				}
			}
		}
	}
	public function setIgnore($ignore, $idx_ignore, $tab, $r_header) {
		$v = $tab->getCellView();
		if($tab->get_height() >= $r_header) {
			$_g1 = 0;
			$_g = $tab->get_width();
			while($_g1 < $_g) {
				$i = $_g1++;
				$name = $v->toString($tab->getCell($i, $r_header));
				if(!$ignore->exists($name)) {
					continue;
				}
				$idx_ignore->set($i, true);
				unset($name,$i);
			}
		}
	}
	public function countActive($active) {
		$ct = 0;
		$showed_dummy = false;
		{
			$_g1 = 0;
			$_g = $active->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$publish = $active->a[$i] > 0;
				$dummy = $active[$i] === 3;
				if($dummy && $showed_dummy) {
					continue;
				}
				if(!$publish) {
					continue;
				}
				$showed_dummy = $dummy;
				$ct++;
				unset($publish,$i,$dummy);
			}
		}
		return $ct;
	}
	public function hilite($output) {
		if(!$output->isResizable()) {
			return false;
		}
		if($this->builder === null) {
			$this->builder = new coopy_FlatCellBuilder();
		}
		$output->resize(0, 0);
		$output->clear();
		$row_map = new haxe_ds_IntMap();
		$col_map = new haxe_ds_IntMap();
		$order = $this->align->toOrder();
		$units = $order->getList();
		$has_parent = $this->align->reference !== null;
		$a = null;
		$b = null;
		$p = null;
		$rp_header = 0;
		$ra_header = 0;
		$rb_header = 0;
		$is_index_p = new haxe_ds_IntMap();
		$is_index_a = new haxe_ds_IntMap();
		$is_index_b = new haxe_ds_IntMap();
		if($has_parent) {
			$p = $this->align->getSource();
			$a = $this->align->reference->getTarget();
			$b = $this->align->getTarget();
			$rp_header = $this->align->reference->meta->getSourceHeader();
			$ra_header = $this->align->reference->meta->getTargetHeader();
			$rb_header = $this->align->meta->getTargetHeader();
			if($this->align->getIndexColumns() !== null) {
				$_g = 0;
				$_g1 = $this->align->getIndexColumns();
				while($_g < $_g1->length) {
					$p2b = $_g1[$_g];
					++$_g;
					if($p2b->l >= 0) {
						$is_index_p->set($p2b->l, true);
					}
					if($p2b->r >= 0) {
						$is_index_b->set($p2b->r, true);
					}
					unset($p2b);
				}
			}
			if($this->align->reference->getIndexColumns() !== null) {
				$_g2 = 0;
				$_g11 = $this->align->reference->getIndexColumns();
				while($_g2 < $_g11->length) {
					$p2a = $_g11[$_g2];
					++$_g2;
					if($p2a->l >= 0) {
						$is_index_p->set($p2a->l, true);
					}
					if($p2a->r >= 0) {
						$is_index_a->set($p2a->r, true);
					}
					unset($p2a);
				}
			}
		} else {
			$a = $this->align->getSource();
			$b = $this->align->getTarget();
			$p = $a;
			$rp_header = $ra_header = $this->align->meta->getSourceHeader();
			$rb_header = $this->align->meta->getTargetHeader();
			if($this->align->getIndexColumns() !== null) {
				$_g3 = 0;
				$_g12 = $this->align->getIndexColumns();
				while($_g3 < $_g12->length) {
					$a2b = $_g12[$_g3];
					++$_g3;
					if($a2b->l >= 0) {
						$is_index_a->set($a2b->l, true);
					}
					if($a2b->r >= 0) {
						$is_index_b->set($a2b->r, true);
					}
					unset($a2b);
				}
			}
		}
		$column_order = $this->align->meta->toOrder();
		$column_units = $column_order->getList();
		$p_ignore = new haxe_ds_IntMap();
		$a_ignore = new haxe_ds_IntMap();
		$b_ignore = new haxe_ds_IntMap();
		$ignore = $this->flags->getIgnoredColumns();
		if($ignore !== null) {
			$this->setIgnore($ignore, $p_ignore, $p, $rp_header);
			$this->setIgnore($ignore, $a_ignore, $a, $ra_header);
			$this->setIgnore($ignore, $b_ignore, $b, $rb_header);
			$ncolumn_units = new _hx_array(array());
			{
				$_g13 = 0;
				$_g4 = $column_units->length;
				while($_g13 < $_g4) {
					$j = $_g13++;
					$cunit = $column_units[$j];
					if($p_ignore->exists($cunit->p) || $a_ignore->exists($cunit->l) || $b_ignore->exists($cunit->r)) {
						continue;
					}
					$ncolumn_units->push($cunit);
					unset($j,$cunit);
				}
			}
			$column_units = $ncolumn_units;
		}
		$show_rc_numbers = false;
		$row_moves = null;
		$col_moves = null;
		if($this->flags->ordered) {
			$row_moves = new haxe_ds_IntMap();
			$moves = coopy_Mover::moveUnits($units);
			{
				$_g14 = 0;
				$_g5 = $moves->length;
				while($_g14 < $_g5) {
					$i = $_g14++;
					{
						$row_moves->set($moves[$i], $i);
						$i;
					}
					unset($i);
				}
			}
			$col_moves = new haxe_ds_IntMap();
			$moves = coopy_Mover::moveUnits($column_units);
			{
				$_g15 = 0;
				$_g6 = $moves->length;
				while($_g15 < $_g6) {
					$i1 = $_g15++;
					{
						$col_moves->set($moves[$i1], $i1);
						$i1;
					}
					unset($i1);
				}
			}
		}
		$active = new _hx_array(array());
		$active_column = null;
		if(!$this->flags->show_unchanged) {
			$_g16 = 0;
			$_g7 = $units->length;
			while($_g16 < $_g7) {
				$i2 = $_g16++;
				$active[$units->length - 1 - $i2] = 0;
				unset($i2);
			}
		}
		$allow_insert = $this->flags->allowInsert();
		$allow_delete = $this->flags->allowDelete();
		$allow_update = $this->flags->allowUpdate();
		if(!$this->flags->show_unchanged_columns) {
			$active_column = new _hx_array(array());
			{
				$_g17 = 0;
				$_g8 = $column_units->length;
				while($_g17 < $_g8) {
					$i3 = $_g17++;
					$v = 0;
					$unit = $column_units[$i3];
					if($unit->l >= 0 && $is_index_a->get($unit->l)) {
						$v = 1;
					}
					if($unit->r >= 0 && $is_index_b->get($unit->r)) {
						$v = 1;
					}
					if($unit->p >= 0 && $is_index_p->get($unit->p)) {
						$v = 1;
					}
					$active_column[$i3] = $v;
					unset($v,$unit,$i3);
				}
			}
		}
		$v1 = $a->getCellView();
		$this->builder->setView($v1);
		$outer_reps_needed = null;
		if($this->flags->show_unchanged && $this->flags->show_unchanged_columns) {
			$outer_reps_needed = 1;
		} else {
			$outer_reps_needed = 2;
		}
		$sep = "";
		$conflict_sep = "";
		$schema = new _hx_array(array());
		$have_schema = false;
		{
			$_g18 = 0;
			$_g9 = $column_units->length;
			while($_g18 < $_g9) {
				$j1 = $_g18++;
				$cunit1 = $column_units[$j1];
				$reordered = false;
				if($this->flags->ordered) {
					if($col_moves->exists($j1)) {
						$reordered = true;
					}
					if($reordered) {
						$show_rc_numbers = true;
					}
				}
				$act = "";
				if($cunit1->r >= 0 && $cunit1->lp() === -1) {
					$have_schema = true;
					$act = "+++";
					if($active_column !== null) {
						if($allow_update) {
							$active_column[$j1] = 1;
						}
					}
				}
				if($cunit1->r < 0 && $cunit1->lp() >= 0) {
					$have_schema = true;
					$act = "---";
					if($active_column !== null) {
						if($allow_update) {
							$active_column[$j1] = 1;
						}
					}
				}
				if($cunit1->r >= 0 && $cunit1->lp() >= 0) {
					if($p->get_height() >= $rp_header && $b->get_height() >= $rb_header) {
						$pp = $p->getCell($cunit1->lp(), $rp_header);
						$bb = $b->getCell($cunit1->r, $rb_header);
						if(!$v1->equals($pp, $bb)) {
							$have_schema = true;
							$act = "(";
							$act .= _hx_string_or_null($v1->toString($pp));
							$act .= ")";
							if($active_column !== null) {
								$active_column[$j1] = 1;
							}
						}
						unset($pp,$bb);
					}
				}
				if($reordered) {
					$act = ":" . _hx_string_or_null($act);
					$have_schema = true;
					if($active_column !== null) {
						$active_column = null;
					}
				}
				$schema->push($act);
				unset($reordered,$j1,$cunit1,$act);
			}
		}
		if($have_schema) {
			$at = $output->get_height();
			$output->resize($column_units->length + 1, $at + 1);
			$output->setCell(0, $at, $this->builder->marker("!"));
			{
				$_g19 = 0;
				$_g10 = $column_units->length;
				while($_g19 < $_g10) {
					$j2 = $_g19++;
					$output->setCell($j2 + 1, $at, $v1->toDatum($schema[$j2]));
					unset($j2);
				}
			}
		}
		$top_line_done = false;
		if($this->flags->always_show_header) {
			$at1 = $output->get_height();
			$output->resize($column_units->length + 1, $at1 + 1);
			$output->setCell(0, $at1, $this->builder->marker("@@"));
			{
				$_g110 = 0;
				$_g20 = $column_units->length;
				while($_g110 < $_g20) {
					$j3 = $_g110++;
					$cunit2 = $column_units[$j3];
					if($cunit2->r >= 0) {
						if($b->get_height() > 0) {
							$output->setCell($j3 + 1, $at1, $b->getCell($cunit2->r, $rb_header));
						}
					} else {
						if($cunit2->lp() >= 0) {
							if($p->get_height() > 0) {
								$output->setCell($j3 + 1, $at1, $p->getCell($cunit2->lp(), $rp_header));
							}
						}
					}
					$col_map->set($j3 + 1, $cunit2);
					unset($j3,$cunit2);
				}
			}
			$top_line_done = true;
		}
		$outer_reps_needed = 2;
		$output_height = $output->get_height();
		$output_height_init = $output->get_height();
		{
			$_g21 = 0;
			while($_g21 < $outer_reps_needed) {
				$out = $_g21++;
				if($out === 1) {
					$this->spreadContext($units, $this->flags->unchanged_context, $active);
					$this->spreadContext($column_units, $this->flags->unchanged_column_context, $active_column);
					if($active_column !== null) {
						$_g22 = 0;
						$_g111 = $column_units->length;
						while($_g22 < $_g111) {
							$i4 = $_g22++;
							if($active_column[$i4] === 3) {
								$active_column[$i4] = 0;
							}
							unset($i4);
						}
						unset($_g22,$_g111);
					}
					$rows = $this->countActive($active) + $output_height_init;
					if($top_line_done) {
						$rows--;
					}
					$output_height = $output_height_init;
					if($rows > $output->get_height()) {
						$output->resize($column_units->length + 1, $rows);
					}
					unset($rows);
				}
				$showed_dummy = false;
				$l = -1;
				$r = -1;
				{
					$_g23 = 0;
					$_g112 = $units->length;
					while($_g23 < $_g112) {
						$i5 = $_g23++;
						$unit1 = $units[$i5];
						$reordered1 = false;
						if($this->flags->ordered) {
							if($row_moves->exists($i5)) {
								$reordered1 = true;
							}
							if($reordered1) {
								$show_rc_numbers = true;
							}
						}
						if($unit1->r < 0 && $unit1->l < 0) {
							continue;
						}
						if($unit1->r === 0 && $unit1->lp() === 0 && $top_line_done) {
							continue;
						}
						$act1 = "";
						if($reordered1) {
							$act1 = ":";
						}
						$publish = $this->flags->show_unchanged;
						$dummy = false;
						if($out === 1) {
							$publish = $active->a[$i5] > 0;
							$dummy = $active[$i5] === 3;
							if($dummy && $showed_dummy) {
								continue;
							}
							if(!$publish) {
								continue;
							}
						}
						if(!$dummy) {
							$showed_dummy = false;
						}
						$at2 = $output_height;
						if($publish) {
							$output_height++;
							if($output->get_height() < $output_height) {
								$output->resize($column_units->length + 1, $output_height);
							}
						}
						if($dummy) {
							{
								$_g41 = 0;
								$_g31 = $column_units->length + 1;
								while($_g41 < $_g31) {
									$j4 = $_g41++;
									$output->setCell($j4, $at2, $v1->toDatum("..."));
									unset($j4);
								}
								unset($_g41,$_g31);
							}
							$showed_dummy = true;
							continue;
						}
						$have_addition = false;
						$skip = false;
						if($unit1->p < 0 && $unit1->l < 0 && $unit1->r >= 0) {
							if(!$allow_insert) {
								$skip = true;
							}
							$act1 = "+++";
						}
						if(($unit1->p >= 0 || !$has_parent) && $unit1->l >= 0 && $unit1->r < 0) {
							if(!$allow_delete) {
								$skip = true;
							}
							$act1 = "---";
						}
						if($skip) {
							if(!$publish) {
								if($active !== null) {
									$active[$i5] = -3;
								}
							}
							continue;
						}
						{
							$_g42 = 0;
							$_g32 = $column_units->length;
							while($_g42 < $_g32) {
								$j5 = $_g42++;
								$cunit3 = $column_units[$j5];
								$pp1 = null;
								$ll = null;
								$rr = null;
								$dd = null;
								$dd_to = null;
								$have_dd_to = false;
								$dd_to_alt = null;
								$have_dd_to_alt = false;
								$have_pp = false;
								$have_ll = false;
								$have_rr = false;
								if($cunit3->p >= 0 && $unit1->p >= 0) {
									$pp1 = $p->getCell($cunit3->p, $unit1->p);
									$have_pp = true;
								}
								if($cunit3->l >= 0 && $unit1->l >= 0) {
									$ll = $a->getCell($cunit3->l, $unit1->l);
									$have_ll = true;
								}
								if($cunit3->r >= 0 && $unit1->r >= 0) {
									$rr = $b->getCell($cunit3->r, $unit1->r);
									$have_rr = true;
									if((coopy_TableDiff_0($this, $_g112, $_g21, $_g23, $_g32, $_g42, $a, $a_ignore, $act1, $active, $active_column, $allow_delete, $allow_insert, $allow_update, $at2, $b, $b_ignore, $col_map, $col_moves, $column_order, $column_units, $conflict_sep, $cunit3, $dd, $dd_to, $dd_to_alt, $dummy, $has_parent, $have_addition, $have_dd_to, $have_dd_to_alt, $have_ll, $have_pp, $have_rr, $have_schema, $i5, $ignore, $is_index_a, $is_index_b, $is_index_p, $j5, $l, $ll, $order, $out, $outer_reps_needed, $output, $output_height, $output_height_init, $p, $p_ignore, $pp1, $publish, $r, $ra_header, $rb_header, $reordered1, $row_map, $row_moves, $rp_header, $rr, $schema, $sep, $show_rc_numbers, $showed_dummy, $skip, $top_line_done, $unit1, $units, $v1)) < 0) {
										if($rr !== null) {
											if($v1->toString($rr) !== "") {
												if($this->flags->allowUpdate()) {
													$have_addition = true;
												}
											}
										}
									}
								}
								if($have_pp) {
									if(!$have_rr) {
										$dd = $pp1;
									} else {
										if($v1->equals($pp1, $rr)) {
											$dd = $pp1;
										} else {
											$dd = $pp1;
											$dd_to = $rr;
											$have_dd_to = true;
											if(!$v1->equals($pp1, $ll)) {
												if(!$v1->equals($pp1, $rr)) {
													$dd_to_alt = $ll;
													$have_dd_to_alt = true;
												}
											}
										}
									}
								} else {
									if($have_ll) {
										if(!$have_rr) {
											$dd = $ll;
										} else {
											if($v1->equals($ll, $rr)) {
												$dd = $ll;
											} else {
												$dd = $ll;
												$dd_to = $rr;
												$have_dd_to = true;
											}
										}
									} else {
										$dd = $rr;
									}
								}
								$cell = $dd;
								if($have_dd_to && $allow_update) {
									if($active_column !== null) {
										$active_column[$j5] = 1;
									}
									if($sep === "") {
										if($this->builder->needSeparator()) {
											$sep = $this->getSeparator($a, $b, "->");
											$this->builder->setSeparator($sep);
										} else {
											$sep = "->";
										}
									}
									$is_conflict = false;
									if($have_dd_to_alt) {
										if(!$v1->equals($dd_to, $dd_to_alt)) {
											$is_conflict = true;
										}
									}
									if(!$is_conflict) {
										$cell = $this->builder->update($dd, $dd_to);
										if(strlen($sep) > strlen($act1)) {
											$act1 = $sep;
										}
									} else {
										if($conflict_sep === "") {
											if($this->builder->needSeparator()) {
												$conflict_sep = _hx_string_or_null($this->getSeparator($p, $a, "!")) . _hx_string_or_null($sep);
												$this->builder->setConflictSeparator($conflict_sep);
											} else {
												$conflict_sep = "!->";
											}
										}
										$cell = $this->builder->conflict($dd, $dd_to_alt, $dd_to);
										$act1 = $conflict_sep;
									}
									unset($is_conflict);
								}
								if($act1 === "" && $have_addition) {
									$act1 = "+";
								}
								if($act1 === "+++") {
									if($have_rr) {
										if($active_column !== null) {
											$active_column[$j5] = 1;
										}
									}
								}
								if($publish) {
									if($active_column === null || $active_column->a[$j5] > 0) {
										$output->setCell($j5 + 1, $at2, $cell);
									}
								}
								unset($rr,$pp1,$ll,$j5,$have_rr,$have_pp,$have_ll,$have_dd_to_alt,$have_dd_to,$dd_to_alt,$dd_to,$dd,$cunit3,$cell);
							}
							unset($_g42,$_g32);
						}
						if($publish) {
							$output->setCell(0, $at2, $this->builder->marker($act1));
							$row_map->set($at2, $unit1);
						}
						if($act1 !== "") {
							if(!$publish) {
								if($active !== null) {
									$active[$i5] = 1;
								}
							}
						}
						unset($unit1,$skip,$reordered1,$publish,$i5,$have_addition,$dummy,$at2,$act1);
					}
					unset($_g23,$_g112);
				}
				unset($showed_dummy,$r,$out,$l);
			}
		}
		if(!$show_rc_numbers) {
			if($this->flags->always_show_order) {
				$show_rc_numbers = true;
			} else {
				if($this->flags->ordered) {
					$show_rc_numbers = $this->isReordered($row_map, $output->get_height());
					if(!$show_rc_numbers) {
						$show_rc_numbers = $this->isReordered($col_map, $output->get_width());
					}
				}
			}
		}
		$admin_w = 1;
		if($show_rc_numbers && !$this->flags->never_show_order) {
			$admin_w++;
			$target = new _hx_array(array());
			{
				$_g113 = 0;
				$_g24 = $output->get_width();
				while($_g113 < $_g24) {
					$i6 = $_g113++;
					$target->push($i6 + 1);
					unset($i6);
				}
			}
			$output->insertOrDeleteColumns($target, $output->get_width() + 1);
			{
				$_g114 = 0;
				$_g25 = $output->get_height();
				while($_g114 < $_g25) {
					$i7 = $_g114++;
					$unit2 = $row_map->get($i7);
					if($unit2 === null) {
						continue;
					}
					$output->setCell(0, $i7, $this->builder->links($unit2));
					unset($unit2,$i7);
				}
			}
			$target = new _hx_array(array());
			{
				$_g115 = 0;
				$_g26 = $output->get_height();
				while($_g115 < $_g26) {
					$i8 = $_g115++;
					$target->push($i8 + 1);
					unset($i8);
				}
			}
			$output->insertOrDeleteRows($target, $output->get_height() + 1);
			{
				$_g116 = 1;
				$_g27 = $output->get_width();
				while($_g116 < $_g27) {
					$i9 = $_g116++;
					$unit3 = $col_map->get($i9 - 1);
					if($unit3 === null) {
						continue;
					}
					$output->setCell($i9, 0, $this->builder->links($unit3));
					unset($unit3,$i9);
				}
			}
			$output->setCell(0, 0, $this->builder->marker("@:@"));
		}
		if($active_column !== null) {
			$all_active = true;
			{
				$_g117 = 0;
				$_g28 = $active_column->length;
				while($_g117 < $_g28) {
					$i10 = $_g117++;
					if($active_column[$i10] === 0) {
						$all_active = false;
						break;
					}
					unset($i10);
				}
			}
			if(!$all_active) {
				$fate = new _hx_array(array());
				{
					$_g29 = 0;
					while($_g29 < $admin_w) {
						$i11 = $_g29++;
						$fate->push($i11);
						unset($i11);
					}
				}
				$at3 = $admin_w;
				$ct = 0;
				$dots = new _hx_array(array());
				{
					$_g118 = 0;
					$_g30 = $active_column->length;
					while($_g118 < $_g30) {
						$i12 = $_g118++;
						$off = $active_column[$i12] === 0;
						if($off) {
							$ct = $ct + 1;
						} else {
							$ct = 0;
						}
						if($off && $ct > 1) {
							$fate->push(-1);
						} else {
							if($off) {
								$dots->push($at3);
							}
							$fate->push($at3);
							$at3++;
						}
						unset($off,$i12);
					}
				}
				$output->insertOrDeleteColumns($fate, $at3);
				{
					$_g33 = 0;
					while($_g33 < $dots->length) {
						$d = $dots[$_g33];
						++$_g33;
						{
							$_g210 = 0;
							$_g119 = $output->get_height();
							while($_g210 < $_g119) {
								$j6 = $_g210++;
								$output->setCell($d, $j6, $this->builder->marker("..."));
								unset($j6);
							}
							unset($_g210,$_g119);
						}
						unset($d);
					}
				}
			}
		}
		return true;
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
	function __toString() { return 'coopy.TableDiff'; }
}
function coopy_TableDiff_0(&$__hx__this, &$_g112, &$_g21, &$_g23, &$_g32, &$_g42, &$a, &$a_ignore, &$act1, &$active, &$active_column, &$allow_delete, &$allow_insert, &$allow_update, &$at2, &$b, &$b_ignore, &$col_map, &$col_moves, &$column_order, &$column_units, &$conflict_sep, &$cunit3, &$dd, &$dd_to, &$dd_to_alt, &$dummy, &$has_parent, &$have_addition, &$have_dd_to, &$have_dd_to_alt, &$have_ll, &$have_pp, &$have_rr, &$have_schema, &$i5, &$ignore, &$is_index_a, &$is_index_b, &$is_index_p, &$j5, &$l, &$ll, &$order, &$out, &$outer_reps_needed, &$output, &$output_height, &$output_height_init, &$p, &$p_ignore, &$pp1, &$publish, &$r, &$ra_header, &$rb_header, &$reordered1, &$row_map, &$row_moves, &$rp_header, &$rr, &$schema, &$sep, &$show_rc_numbers, &$showed_dummy, &$skip, &$top_line_done, &$unit1, &$units, &$v1) {
	if($have_pp) {
		return $cunit3->p;
	} else {
		return $cunit3->l;
	}
}
