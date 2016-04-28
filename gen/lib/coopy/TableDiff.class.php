<?php

class coopy_TableDiff {
	public function __construct($align, $flags) {
		if(!php_Boot::$skip_constructor) {
		$this->align = $align;
		$this->flags = $flags;
		$this->builder = null;
		$this->preserve_columns = false;
	}}
	public $align;
	public $flags;
	public $builder;
	public $row_map;
	public $col_map;
	public $has_parent;
	public $a;
	public $b;
	public $p;
	public $rp_header;
	public $ra_header;
	public $rb_header;
	public $is_index_p;
	public $is_index_a;
	public $is_index_b;
	public $order;
	public $row_units;
	public $column_units;
	public $show_rc_numbers;
	public $row_moves;
	public $col_moves;
	public $active_row;
	public $active_column;
	public $allow_insert;
	public $allow_delete;
	public $allow_update;
	public $v;
	public $sep;
	public $conflict_sep;
	public $schema;
	public $have_schema;
	public $top_line_done;
	public $have_addition;
	public $act;
	public $publish;
	public $diff_found;
	public $schema_diff_found;
	public $preserve_columns;
	public $nested;
	public $nesting_present;
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
	public function reset() {
		$this->has_parent = false;
		$this->rp_header = $this->ra_header = $this->rb_header = 0;
		$this->is_index_p = new haxe_ds_IntMap();
		$this->is_index_a = new haxe_ds_IntMap();
		$this->is_index_b = new haxe_ds_IntMap();
		$this->row_map = new haxe_ds_IntMap();
		$this->col_map = new haxe_ds_IntMap();
		$this->show_rc_numbers = false;
		$this->row_moves = null;
		$this->col_moves = null;
		$this->allow_insert = $this->allow_delete = $this->allow_update = true;
		$this->sep = "";
		$this->conflict_sep = "";
		$this->top_line_done = false;
		$this->diff_found = false;
		$this->schema_diff_found = false;
	}
	public function setupTables() {
		$this->order = $this->align->toOrder();
		$this->row_units = $this->order->getList();
		$this->has_parent = $this->align->reference !== null;
		if($this->has_parent) {
			$this->p = $this->align->getSource();
			$this->a = $this->align->reference->getTarget();
			$this->b = $this->align->getTarget();
			$this->rp_header = $this->align->reference->meta->getSourceHeader();
			$this->ra_header = $this->align->reference->meta->getTargetHeader();
			$this->rb_header = $this->align->meta->getTargetHeader();
			if($this->align->getIndexColumns() !== null) {
				$_g = 0;
				$_g1 = $this->align->getIndexColumns();
				while($_g < $_g1->length) {
					$p2b = $_g1[$_g];
					++$_g;
					if($p2b->l >= 0) {
						$this->is_index_p->set($p2b->l, true);
					}
					if($p2b->r >= 0) {
						$this->is_index_b->set($p2b->r, true);
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
						$this->is_index_p->set($p2a->l, true);
					}
					if($p2a->r >= 0) {
						$this->is_index_a->set($p2a->r, true);
					}
					unset($p2a);
				}
			}
		} else {
			$this->a = $this->align->getSource();
			$this->b = $this->align->getTarget();
			$this->p = $this->a;
			$this->ra_header = $this->align->meta->getSourceHeader();
			$this->rp_header = $this->ra_header;
			$this->rb_header = $this->align->meta->getTargetHeader();
			if($this->align->getIndexColumns() !== null) {
				$_g3 = 0;
				$_g12 = $this->align->getIndexColumns();
				while($_g3 < $_g12->length) {
					$a2b = $_g12[$_g3];
					++$_g3;
					if($a2b->l >= 0) {
						$this->is_index_a->set($a2b->l, true);
					}
					if($a2b->r >= 0) {
						$this->is_index_b->set($a2b->r, true);
					}
					unset($a2b);
				}
			}
		}
		$this->allow_insert = $this->flags->allowInsert();
		$this->allow_delete = $this->flags->allowDelete();
		$this->allow_update = $this->flags->allowUpdate();
		$common = $this->a;
		if($common === null) {
			$common = $this->b;
		}
		if($common === null) {
			$common = $this->p;
		}
		$this->v = $common->getCellView();
		$this->builder->setView($this->v);
		$this->nested = false;
		$meta = $common->getMeta();
		if($meta !== null) {
			$this->nested = $meta->isNested();
		}
		$this->nesting_present = false;
	}
	public function scanActivity() {
		$this->active_row = new _hx_array(array());
		$this->active_column = null;
		if(!$this->flags->show_unchanged) {
			$_g1 = 0;
			$_g = $this->row_units->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$this->active_row[$this->row_units->length - 1 - $i] = 0;
				unset($i);
			}
		}
		if(!$this->flags->show_unchanged_columns) {
			$this->active_column = new _hx_array(array());
			{
				$_g11 = 0;
				$_g2 = $this->column_units->length;
				while($_g11 < $_g2) {
					$i1 = $_g11++;
					$v = 0;
					$unit = $this->column_units[$i1];
					if($unit->l >= 0 && $this->is_index_a->get($unit->l)) {
						$v = 1;
					}
					if($unit->r >= 0 && $this->is_index_b->get($unit->r)) {
						$v = 1;
					}
					if($unit->p >= 0 && $this->is_index_p->get($unit->p)) {
						$v = 1;
					}
					$this->active_column[$i1] = $v;
					unset($v,$unit,$i1);
				}
			}
		}
	}
	public function setupColumns() {
		$column_order = $this->align->meta->toOrder();
		$this->column_units = $column_order->getList();
		$ignore = $this->flags->getIgnoredColumns();
		if($ignore !== null) {
			$p_ignore = new haxe_ds_IntMap();
			$a_ignore = new haxe_ds_IntMap();
			$b_ignore = new haxe_ds_IntMap();
			$this->setIgnore($ignore, $p_ignore, $this->p, $this->rp_header);
			$this->setIgnore($ignore, $a_ignore, $this->a, $this->ra_header);
			$this->setIgnore($ignore, $b_ignore, $this->b, $this->rb_header);
			$ncolumn_units = new _hx_array(array());
			{
				$_g1 = 0;
				$_g = $this->column_units->length;
				while($_g1 < $_g) {
					$j = $_g1++;
					$cunit = $this->column_units[$j];
					if($p_ignore->exists($cunit->p) || $a_ignore->exists($cunit->l) || $b_ignore->exists($cunit->r)) {
						continue;
					}
					$ncolumn_units->push($cunit);
					unset($j,$cunit);
				}
			}
			$this->column_units = $ncolumn_units;
		}
	}
	public function setupMoves() {
		if($this->flags->ordered) {
			$this->row_moves = new haxe_ds_IntMap();
			$moves = coopy_Mover::moveUnits($this->row_units);
			{
				$_g1 = 0;
				$_g = $moves->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					{
						$this->row_moves->set($moves[$i], $i);
						$i;
					}
					unset($i);
				}
			}
			$this->col_moves = new haxe_ds_IntMap();
			$moves = coopy_Mover::moveUnits($this->column_units);
			{
				$_g11 = 0;
				$_g2 = $moves->length;
				while($_g11 < $_g2) {
					$i1 = $_g11++;
					{
						$this->col_moves->set($moves[$i1], $i1);
						$i1;
					}
					unset($i1);
				}
			}
		}
	}
	public function scanSchema() {
		$this->schema = new _hx_array(array());
		$this->have_schema = false;
		{
			$_g1 = 0;
			$_g = $this->column_units->length;
			while($_g1 < $_g) {
				$j = $_g1++;
				$cunit = $this->column_units[$j];
				$reordered = false;
				if($this->flags->ordered) {
					if($this->col_moves->exists($j)) {
						$reordered = true;
					}
					if($reordered) {
						$this->show_rc_numbers = true;
					}
				}
				$act = "";
				if($cunit->r >= 0 && $cunit->lp() === -1) {
					$this->have_schema = true;
					$act = "+++";
					if($this->active_column !== null) {
						if($this->allow_update) {
							$this->active_column[$j] = 1;
						}
					}
				}
				if($cunit->r < 0 && $cunit->lp() >= 0) {
					$this->have_schema = true;
					$act = "---";
					if($this->active_column !== null) {
						if($this->allow_update) {
							$this->active_column[$j] = 1;
						}
					}
				}
				if($cunit->r >= 0 && $cunit->lp() >= 0) {
					if($this->p->get_height() >= $this->rp_header && $this->b->get_height() >= $this->rb_header) {
						$pp = $this->p->getCell($cunit->lp(), $this->rp_header);
						$bb = $this->b->getCell($cunit->r, $this->rb_header);
						if(!$this->isEqual($this->v, $pp, $bb)) {
							$this->have_schema = true;
							$act = "(";
							$act .= _hx_string_or_null($this->v->toString($pp));
							$act .= ")";
							if($this->active_column !== null) {
								$this->active_column[$j] = 1;
							}
						}
						unset($pp,$bb);
					}
				}
				if($reordered) {
					$act = ":" . _hx_string_or_null($act);
					$this->have_schema = true;
					if($this->active_column !== null) {
						$this->active_column = null;
					}
				}
				$this->schema->push($act);
				unset($reordered,$j,$cunit,$act);
			}
		}
	}
	public function checkRcNumbers($w, $h) {
		if(!$this->show_rc_numbers) {
			if($this->flags->always_show_order) {
				$this->show_rc_numbers = true;
			} else {
				if($this->flags->ordered) {
					$this->show_rc_numbers = $this->isReordered($this->row_map, $h);
					if(!$this->show_rc_numbers) {
						$this->show_rc_numbers = $this->isReordered($this->col_map, $w);
					}
				}
			}
		}
	}
	public function addRcNumbers($output) {
		$admin_w = 1;
		if($this->show_rc_numbers && !$this->flags->never_show_order) {
			$admin_w++;
			$target = new _hx_array(array());
			{
				$_g1 = 0;
				$_g = $output->get_width();
				while($_g1 < $_g) {
					$i = $_g1++;
					$target->push($i + 1);
					unset($i);
				}
			}
			$output->insertOrDeleteColumns($target, $output->get_width() + 1);
			{
				$_g11 = 0;
				$_g2 = $output->get_height();
				while($_g11 < $_g2) {
					$i1 = $_g11++;
					$unit = $this->row_map->get($i1);
					if($unit === null) {
						$output->setCell(0, $i1, "");
						continue;
					}
					$output->setCell(0, $i1, $this->builder->links($unit, true));
					unset($unit,$i1);
				}
			}
			$target = new _hx_array(array());
			{
				$_g12 = 0;
				$_g3 = $output->get_height();
				while($_g12 < $_g3) {
					$i2 = $_g12++;
					$target->push($i2 + 1);
					unset($i2);
				}
			}
			$output->insertOrDeleteRows($target, $output->get_height() + 1);
			{
				$_g13 = 1;
				$_g4 = $output->get_width();
				while($_g13 < $_g4) {
					$i3 = $_g13++;
					$unit1 = $this->col_map->get($i3 - 1);
					if($unit1 === null) {
						$output->setCell($i3, 0, "");
						continue;
					}
					$output->setCell($i3, 0, $this->builder->links($unit1, false));
					unset($unit1,$i3);
				}
			}
			$output->setCell(0, 0, $this->builder->marker("@:@"));
		}
		return $admin_w;
	}
	public function elideColumns($output, $admin_w) {
		if($this->active_column !== null) {
			$all_active = true;
			{
				$_g1 = 0;
				$_g = $this->active_column->length;
				while($_g1 < $_g) {
					$i = $_g1++;
					if($this->active_column[$i] === 0) {
						$all_active = false;
						break;
					}
					unset($i);
				}
			}
			if(!$all_active) {
				$fate = new _hx_array(array());
				{
					$_g2 = 0;
					while($_g2 < $admin_w) {
						$i1 = $_g2++;
						$fate->push($i1);
						unset($i1);
					}
				}
				$at = $admin_w;
				$ct = 0;
				$dots = new _hx_array(array());
				{
					$_g11 = 0;
					$_g3 = $this->active_column->length;
					while($_g11 < $_g3) {
						$i2 = $_g11++;
						$off = $this->active_column[$i2] === 0;
						if($off) {
							$ct = $ct + 1;
						} else {
							$ct = 0;
						}
						if($off && $ct > 1) {
							$fate->push(-1);
						} else {
							if($off) {
								$dots->push($at);
							}
							$fate->push($at);
							$at++;
						}
						unset($off,$i2);
					}
				}
				$output->insertOrDeleteColumns($fate, $at);
				{
					$_g4 = 0;
					while($_g4 < $dots->length) {
						$d = $dots[$_g4];
						++$_g4;
						{
							$_g21 = 0;
							$_g12 = $output->get_height();
							while($_g21 < $_g12) {
								$j = $_g21++;
								$output->setCell($d, $j, $this->builder->marker("..."));
								unset($j);
							}
							unset($_g21,$_g12);
						}
						unset($d);
					}
				}
			}
		}
	}
	public function addSchema($output) {
		if($this->have_schema) {
			$at = $output->get_height();
			$output->resize($this->column_units->length + 1, $at + 1);
			$output->setCell(0, $at, $this->builder->marker("!"));
			{
				$_g1 = 0;
				$_g = $this->column_units->length;
				while($_g1 < $_g) {
					$j = $_g1++;
					$output->setCell($j + 1, $at, $this->v->toDatum($this->schema[$j]));
					unset($j);
				}
			}
			$this->schema_diff_found = true;
		}
	}
	public function addHeader($output) {
		if($this->flags->always_show_header) {
			$at = $output->get_height();
			$output->resize($this->column_units->length + 1, $at + 1);
			$output->setCell(0, $at, $this->builder->marker("@@"));
			{
				$_g1 = 0;
				$_g = $this->column_units->length;
				while($_g1 < $_g) {
					$j = $_g1++;
					$cunit = $this->column_units[$j];
					if($cunit->r >= 0) {
						if($this->b->get_height() !== 0) {
							$output->setCell($j + 1, $at, $this->b->getCell($cunit->r, $this->rb_header));
						}
					} else {
						if($cunit->l >= 0) {
							if($this->a->get_height() !== 0) {
								$output->setCell($j + 1, $at, $this->a->getCell($cunit->l, $this->ra_header));
							}
						} else {
							if($cunit->lp() >= 0) {
								if($this->p->get_height() !== 0) {
									$output->setCell($j + 1, $at, $this->p->getCell($cunit->lp(), $this->rp_header));
								}
							}
						}
					}
					$this->col_map->set($j + 1, $cunit);
					unset($j,$cunit);
				}
			}
			$this->top_line_done = true;
		}
	}
	public function checkMeta($t, $meta) {
		if($meta->get_width() !== $t->get_width() + 1) {
			return false;
		}
		if($meta->get_width() === 0 || $meta->get_height() === 0) {
			return false;
		}
		return true;
	}
	public function getMetaTable($t) {
		if($t === null) {
			return null;
		}
		$meta = $t->getMeta();
		if($meta === null) {
			return null;
		}
		return $meta->asTable();
	}
	public function addMeta($output) {
		$a_meta = null;
		$b_meta = null;
		$p_meta = null;
		$a_meta = $this->getMetaTable($this->a);
		$b_meta = $this->getMetaTable($this->b);
		$p_meta = $this->getMetaTable($this->p);
		if($a_meta === null || $b_meta === null || $p_meta === null) {
			return false;
		}
		if(!$this->checkMeta($this->a, $a_meta)) {
			return false;
		}
		if(!$this->checkMeta($this->b, $b_meta)) {
			return false;
		}
		if(!$this->checkMeta($this->p, $p_meta)) {
			return false;
		}
		if(!$this->flags->show_meta) {
			return false;
		}
		$meta_diff = new coopy_SimpleTable(0, 0);
		$meta_flags = new coopy_CompareFlags();
		$meta_flags->addPrimaryKey("@@");
		$meta_flags->addPrimaryKey("@");
		$meta_flags->unchanged_column_context = 65536;
		$meta_flags->unchanged_context = 0;
		$meta_align = coopy_Coopy::compareTables3((($a_meta === $p_meta) ? null : $p_meta), $a_meta, $b_meta, $meta_flags)->align();
		$td = new coopy_TableDiff($meta_align, $meta_flags);
		$td->preserve_columns = true;
		$td->hilite($meta_diff);
		if($td->hasDifference()) {
			$h = $output->get_height();
			$dh = $meta_diff->get_height();
			$offset = null;
			if($td->hasSchemaDifference()) {
				$offset = 2;
			} else {
				$offset = 1;
			}
			$output->resize($output->get_width(), $h + $dh - $offset);
			$v = $meta_diff->getCellView();
			{
				$_g = $offset;
				while($_g < $dh) {
					$y = $_g++;
					{
						$_g2 = 1;
						$_g1 = $meta_diff->get_width();
						while($_g2 < $_g1) {
							$x = $_g2++;
							$c = $meta_diff->getCell($x, $y);
							if($x === 1) {
								$c = "@" . _hx_string_or_null($v->toString($c)) . "@" . _hx_string_or_null($v->toString($meta_diff->getCell(0, $y)));
							}
							$output->setCell($x - 1, $h + $y - $offset, $c);
							unset($x,$c);
						}
						unset($_g2,$_g1);
					}
					unset($y);
				}
			}
			if($this->active_column !== null) {
				if($td->active_column->length === $meta_diff->get_width()) {
					$_g11 = 1;
					$_g3 = $meta_diff->get_width();
					while($_g11 < $_g3) {
						$i = $_g11++;
						if($td->active_column->a[$i] >= 0) {
							$this->active_column[$i - 1] = 1;
						}
						unset($i);
					}
				}
			}
		}
		return false;
	}
	public function refineActivity() {
		$this->spreadContext($this->row_units, $this->flags->unchanged_context, $this->active_row);
		$this->spreadContext($this->column_units, $this->flags->unchanged_column_context, $this->active_column);
		if($this->active_column !== null) {
			$_g1 = 0;
			$_g = $this->column_units->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if($this->active_column[$i] === 3) {
					$this->active_column[$i] = 0;
				}
				unset($i);
			}
		}
	}
	public function normalizeString($v, $str) {
		if($str === null) {
			return $str;
		}
		if(!($this->flags->ignore_whitespace || $this->flags->ignore_case)) {
			return $str;
		}
		$txt = $v->toString($str);
		if($this->flags->ignore_whitespace) {
			$txt = trim($txt);
		}
		if($this->flags->ignore_case) {
			$txt = strtolower($txt);
		}
		return $txt;
	}
	public function isEqual($v, $aa, $bb) {
		if($this->flags->ignore_whitespace || $this->flags->ignore_case) {
			return $this->normalizeString($v, $aa) === $this->normalizeString($v, $bb);
		}
		return $v->equals($aa, $bb);
	}
	public function checkNesting($v, $have_ll, $ll, $have_rr, $rr, $have_pp, $pp, $x, $y) {
		$all_tables = true;
		if($have_ll) {
			$all_tables = $all_tables && $v->isTable($ll);
		}
		if($have_rr) {
			$all_tables = $all_tables && $v->isTable($rr);
		}
		if($have_pp) {
			$all_tables = $all_tables && $v->isTable($pp);
		}
		if(!$all_tables) {
			return (new _hx_array(array($ll, $rr, $pp)));
		}
		$ll_table = null;
		$rr_table = null;
		$pp_table = null;
		if($have_ll) {
			$ll_table = $v->getTable($ll);
		}
		if($have_rr) {
			$rr_table = $v->getTable($rr);
		}
		if($have_pp) {
			$pp_table = $v->getTable($pp);
		}
		$compare = false;
		$comp = new coopy_TableComparisonState();
		$comp->a = $ll_table;
		$comp->b = $rr_table;
		$comp->p = $pp_table;
		$comp->compare_flags = $this->flags;
		$comp->getMeta();
		$key = null;
		if($comp->a_meta !== null) {
			$key = $comp->a_meta->getName();
		}
		if($key === null && $comp->b_meta !== null) {
			$key = $comp->b_meta->getName();
		}
		if($key === null) {
			$key = _hx_string_rec($x, "") . "_" . _hx_string_rec($y, "");
		}
		if($this->align->comp !== null) {
			if($this->align->comp->children === null) {
				$this->align->comp->children = new haxe_ds_StringMap();
				$this->align->comp->child_order = new _hx_array(array());
				$compare = true;
			} else {
				$compare = !$this->align->comp->children->exists($key);
			}
		}
		if($compare) {
			$this->nesting_present = true;
			$this->align->comp->children->set($key, $comp);
			$this->align->comp->child_order->push($key);
			$ct = new coopy_CompareTable($comp);
			$ct->align();
		} else {
			$comp = $this->align->comp->children->get($key);
		}
		$ll_out = null;
		$rr_out = null;
		$pp_out = null;
		if($comp->alignment->isMarkedAsIdentical() || $have_ll && !$have_rr || $have_rr && !$have_ll) {
			$ll_out = "[" . _hx_string_or_null($key) . "]";
			$rr_out = $ll_out;
			$pp_out = $ll_out;
		} else {
			if($ll !== null) {
				$ll_out = "[a." . _hx_string_or_null($key) . "]";
			}
			if($rr !== null) {
				$rr_out = "[b." . _hx_string_or_null($key) . "]";
			}
			if($pp !== null) {
				$pp_out = "[p." . _hx_string_or_null($key) . "]";
			}
		}
		return (new _hx_array(array($ll_out, $rr_out, $pp_out)));
	}
	public function scanRow($unit, $output, $at, $i) {
		{
			$_g1 = 0;
			$_g = $this->column_units->length;
			while($_g1 < $_g) {
				$j = $_g1++;
				$cunit = $this->column_units[$j];
				$pp = null;
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
				if($cunit->p >= 0 && $unit->p >= 0) {
					$pp = $this->p->getCell($cunit->p, $unit->p);
					$have_pp = true;
				}
				if($cunit->l >= 0 && $unit->l >= 0) {
					$ll = $this->a->getCell($cunit->l, $unit->l);
					$have_ll = true;
				}
				if($cunit->r >= 0 && $unit->r >= 0) {
					$rr = $this->b->getCell($cunit->r, $unit->r);
					$have_rr = true;
					if((coopy_TableDiff_0($this, $_g, $_g1, $at, $cunit, $dd, $dd_to, $dd_to_alt, $have_dd_to, $have_dd_to_alt, $have_ll, $have_pp, $have_rr, $i, $j, $ll, $output, $pp, $rr, $unit)) < 0) {
						if($rr !== null) {
							if($this->v->toString($rr) !== "") {
								if($this->flags->allowUpdate()) {
									$this->have_addition = true;
								}
							}
						}
					}
				}
				if($this->nested) {
					$ndiff = $this->checkNesting($this->v, $have_ll, $ll, $have_rr, $rr, $have_pp, $pp, $i, $j);
					$ll = $ndiff[0];
					$rr = $ndiff[1];
					$pp = $ndiff[2];
					unset($ndiff);
				}
				if($have_pp) {
					if(!$have_rr) {
						$dd = $pp;
					} else {
						if($this->isEqual($this->v, $pp, $rr)) {
							$dd = $ll;
						} else {
							$dd = $pp;
							$dd_to = $rr;
							$have_dd_to = true;
							if(!$this->isEqual($this->v, $pp, $ll)) {
								if(!$this->isEqual($this->v, $pp, $rr)) {
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
							if($this->isEqual($this->v, $ll, $rr)) {
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
				if($have_dd_to && $this->allow_update) {
					if($this->active_column !== null) {
						$this->active_column[$j] = 1;
					}
					if($this->sep === "") {
						if($this->builder->needSeparator()) {
							$this->sep = $this->getSeparator($this->a, $this->b, "->");
							$this->builder->setSeparator($this->sep);
						} else {
							$this->sep = "->";
						}
					}
					$is_conflict = false;
					if($have_dd_to_alt) {
						if(!$this->isEqual($this->v, $dd_to, $dd_to_alt)) {
							$is_conflict = true;
						}
					}
					if(!$is_conflict) {
						$cell = $this->builder->update($dd, $dd_to);
						if(strlen($this->sep) > strlen($this->act)) {
							$this->act = $this->sep;
						}
					} else {
						if($this->conflict_sep === "") {
							if($this->builder->needSeparator()) {
								$this->conflict_sep = _hx_string_or_null($this->getSeparator($this->p, $this->a, "!")) . _hx_string_or_null($this->sep);
								$this->builder->setConflictSeparator($this->conflict_sep);
							} else {
								$this->conflict_sep = "!->";
							}
						}
						$cell = $this->builder->conflict($dd, $dd_to_alt, $dd_to);
						$this->act = $this->conflict_sep;
					}
					unset($is_conflict);
				}
				if($this->act === "" && $this->have_addition) {
					$this->act = "+";
				}
				if($this->act === "+++") {
					if($have_rr) {
						if($this->active_column !== null) {
							$this->active_column[$j] = 1;
						}
					}
				}
				if($this->publish) {
					if($this->active_column === null || $this->active_column->a[$j] > 0) {
						$output->setCell($j + 1, $at, $cell);
					}
				}
				unset($rr,$pp,$ll,$j,$have_rr,$have_pp,$have_ll,$have_dd_to_alt,$have_dd_to,$dd_to_alt,$dd_to,$dd,$cunit,$cell);
			}
		}
		if($this->publish) {
			$output->setCell(0, $at, $this->builder->marker($this->act));
			$this->row_map->set($at, $unit);
		}
		if($this->act !== "") {
			$this->diff_found = true;
			if(!$this->publish) {
				if($this->active_row !== null) {
					$this->active_row[$i] = 1;
				}
			}
		}
	}
	public function hilite($output) {
		$output = coopy_Coopy::tablify($output);
		return $this->hiliteSingle($output);
	}
	public function hiliteSingle($output) {
		if(!$output->isResizable()) {
			return false;
		}
		if($this->builder === null) {
			if($this->flags->allow_nested_cells) {
				$this->builder = new coopy_NestedCellBuilder();
			} else {
				$this->builder = new coopy_FlatCellBuilder($this->flags);
			}
		}
		$output->resize(0, 0);
		$output->clear();
		$this->reset();
		$this->setupTables();
		$this->setupColumns();
		$this->setupMoves();
		$this->scanActivity();
		$this->scanSchema();
		$this->addSchema($output);
		$this->addHeader($output);
		$this->addMeta($output);
		$outer_reps_needed = null;
		if($this->flags->show_unchanged && $this->flags->show_unchanged_columns) {
			$outer_reps_needed = 1;
		} else {
			$outer_reps_needed = 2;
		}
		$outer_reps_needed = 2;
		$output_height = $output->get_height();
		$output_height_init = $output->get_height();
		{
			$_g = 0;
			while($_g < $outer_reps_needed) {
				$out = $_g++;
				if($out === 1) {
					$this->refineActivity();
					$rows = $this->countActive($this->active_row) + $output_height_init;
					if($this->top_line_done) {
						$rows--;
					}
					$output_height = $output_height_init;
					if($rows > $output->get_height()) {
						$output->resize($this->column_units->length + 1, $rows);
					}
					unset($rows);
				}
				$showed_dummy = false;
				$l = -1;
				$r = -1;
				{
					$_g2 = 0;
					$_g1 = $this->row_units->length;
					while($_g2 < $_g1) {
						$i = $_g2++;
						$unit = $this->row_units[$i];
						$reordered = false;
						if($this->flags->ordered) {
							if($this->row_moves->exists($i)) {
								$reordered = true;
							}
							if($reordered) {
								$this->show_rc_numbers = true;
							}
						}
						if($unit->r < 0 && $unit->l < 0) {
							continue;
						}
						if($unit->r === 0 && $unit->lp() <= 0 && $this->top_line_done) {
							continue;
						}
						$this->publish = $this->flags->show_unchanged;
						$dummy = false;
						if($out === 1) {
							$this->publish = $this->active_row->a[$i] > 0;
							$dummy = $this->active_row[$i] === 3;
							if($dummy && $showed_dummy) {
								continue;
							}
							if(!$this->publish) {
								continue;
							}
						}
						if(!$dummy) {
							$showed_dummy = false;
						}
						$at = $output_height;
						if($this->publish) {
							$output_height++;
							if($output->get_height() < $output_height) {
								$output->resize($this->column_units->length + 1, $output_height);
							}
						}
						if($dummy) {
							{
								$_g4 = 0;
								$_g3 = $this->column_units->length + 1;
								while($_g4 < $_g3) {
									$j = $_g4++;
									$output->setCell($j, $at, $this->v->toDatum("..."));
									unset($j);
								}
								unset($_g4,$_g3);
							}
							$showed_dummy = true;
							continue;
						}
						$this->have_addition = false;
						$skip = false;
						$this->act = "";
						if($reordered) {
							$this->act = ":";
						}
						if($unit->p < 0 && $unit->l < 0 && $unit->r >= 0) {
							if(!$this->allow_insert) {
								$skip = true;
							}
							$this->act = "+++";
						}
						if(($unit->p >= 0 || !$this->has_parent) && $unit->l >= 0 && $unit->r < 0) {
							if(!$this->allow_delete) {
								$skip = true;
							}
							$this->act = "---";
						}
						if($skip) {
							if(!$this->publish) {
								if($this->active_row !== null) {
									$this->active_row[$i] = -3;
								}
							}
							continue;
						}
						$this->scanRow($unit, $output, $at, $i);
						unset($unit,$skip,$reordered,$i,$dummy,$at);
					}
					unset($_g2,$_g1);
				}
				unset($showed_dummy,$r,$out,$l);
			}
		}
		$this->checkRcNumbers($output->get_width(), $output->get_height());
		$admin_w = $this->addRcNumbers($output);
		if(!$this->preserve_columns) {
			$this->elideColumns($output, $admin_w);
		}
		return true;
	}
	public function hiliteWithNesting($output) {
		$base = $output->add("base");
		$result = $this->hiliteSingle($base);
		if(!$result) {
			return false;
		}
		if($this->align->comp === null) {
			return true;
		}
		$order = $this->align->comp->child_order;
		if($order === null) {
			return true;
		}
		$output->alignment = $this->align;
		{
			$_g = 0;
			while($_g < $order->length) {
				$name = $order[$_g];
				++$_g;
				$child = $this->align->comp->children->get($name);
				$alignment = $child->alignment;
				if($alignment->isMarkedAsIdentical()) {
					$this->align->comp->children->set($name, null);
					continue;
				}
				$td = new coopy_TableDiff($alignment, $this->flags);
				$child_output = $output->add($name);
				$result = $result && $td->hiliteSingle($child_output);
				unset($td,$name,$child_output,$child,$alignment);
			}
		}
		return $result;
	}
	public function hasDifference() {
		return $this->diff_found;
	}
	public function hasSchemaDifference() {
		return $this->schema_diff_found;
	}
	public function isNested() {
		return $this->nesting_present;
	}
	public function getComparisonState() {
		if($this->align === null) {
			return null;
		}
		return $this->align->comp;
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
function coopy_TableDiff_0(&$__hx__this, &$_g, &$_g1, &$at, &$cunit, &$dd, &$dd_to, &$dd_to_alt, &$have_dd_to, &$have_dd_to_alt, &$have_ll, &$have_pp, &$have_rr, &$i, &$j, &$ll, &$output, &$pp, &$rr, &$unit) {
	if($have_pp) {
		return $cunit->p;
	} else {
		return $cunit->l;
	}
}
