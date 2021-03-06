<?php

class coopy_Merger {
	public function __construct($parent, $local, $remote, $flags) {
		if(!php_Boot::$skip_constructor) {
		$this->parent = $parent;
		$this->local = $local;
		$this->remote = $remote;
		$this->flags = $flags;
	}}
	public $parent;
	public $local;
	public $remote;
	public $flags;
	public $order;
	public $units;
	public $column_order;
	public $column_units;
	public $row_mix_local;
	public $row_mix_remote;
	public $column_mix_local;
	public $column_mix_remote;
	public $conflicts;
	public function shuffleDimension($dim_units, $len, $fate, $cl, $cr) {
		$at = 0;
		{
			$_g = 0;
			while($_g < $dim_units->length) {
				$cunit = $dim_units[$_g];
				++$_g;
				if($cunit->p < 0) {
					if($cunit->l < 0) {
						if($cunit->r >= 0) {
							{
								$cr->set($cunit->r, $at);
								$at;
							}
							$at++;
						}
					} else {
						{
							$cl->set($cunit->l, $at);
							$at;
						}
						$at++;
					}
				} else {
					if($cunit->l >= 0) {
						if($cunit->r < 0) {} else {
							{
								$cl->set($cunit->l, $at);
								$at;
							}
							$at++;
						}
					}
				}
				unset($cunit);
			}
		}
		{
			$_g1 = 0;
			while($_g1 < $len) {
				$x = $_g1++;
				$idx = $cl->get($x);
				if($idx === null) {
					$fate->push(-1);
				} else {
					$fate->push($idx);
				}
				unset($x,$idx);
			}
		}
		return $at;
	}
	public function shuffleColumns() {
		$this->column_mix_local = new haxe_ds_IntMap();
		$this->column_mix_remote = new haxe_ds_IntMap();
		$fate = new _hx_array(array());
		$wfate = $this->shuffleDimension($this->column_units, $this->local->get_width(), $fate, $this->column_mix_local, $this->column_mix_remote);
		$this->local->insertOrDeleteColumns($fate, $wfate);
	}
	public function shuffleRows() {
		$this->row_mix_local = new haxe_ds_IntMap();
		$this->row_mix_remote = new haxe_ds_IntMap();
		$fate = new _hx_array(array());
		$hfate = $this->shuffleDimension($this->units, $this->local->get_height(), $fate, $this->row_mix_local, $this->row_mix_remote);
		$this->local->insertOrDeleteRows($fate, $hfate);
	}
	public function apply() {
		$this->conflicts = 0;
		$ct = coopy_Coopy::compareTables3($this->parent, $this->local, $this->remote, null);
		$align = $ct->align();
		$this->order = $align->toOrder();
		$this->units = $this->order->getList();
		$this->column_order = $align->meta->toOrder();
		$this->column_units = $this->column_order->getList();
		$allow_insert = $this->flags->allowInsert();
		$allow_delete = $this->flags->allowDelete();
		$allow_update = $this->flags->allowUpdate();
		$view = $this->parent->getCellView();
		{
			$_g = 0;
			$_g1 = $this->units;
			while($_g < $_g1->length) {
				$row = $_g1[$_g];
				++$_g;
				if($row->l >= 0 && $row->r >= 0 && $row->p >= 0) {
					$_g2 = 0;
					$_g3 = $this->column_units;
					while($_g2 < $_g3->length) {
						$col = $_g3[$_g2];
						++$_g2;
						if($col->l >= 0 && $col->r >= 0 && $col->p >= 0) {
							$pcell = $this->parent->getCell($col->p, $row->p);
							$rcell = $this->remote->getCell($col->r, $row->r);
							if(!$view->equals($pcell, $rcell)) {
								$lcell = $this->local->getCell($col->l, $row->l);
								if($view->equals($pcell, $lcell)) {
									$this->local->setCell($col->l, $row->l, $rcell);
								} else {
									$this->local->setCell($col->l, $row->l, coopy_Merger::makeConflictedCell($view, $pcell, $lcell, $rcell));
									$this->conflicts++;
								}
								unset($lcell);
							}
							unset($rcell,$pcell);
						}
						unset($col);
					}
					unset($_g3,$_g2);
				}
				unset($row);
			}
		}
		$this->shuffleColumns();
		$this->shuffleRows();
		if(null == $this->column_mix_remote) throw new HException('null iterable');
		$__hx__it = $this->column_mix_remote->keys();
		while($__hx__it->hasNext()) {
			unset($x);
			$x = $__hx__it->next();
			$x2 = $this->column_mix_remote->get($x);
			{
				$_g4 = 0;
				$_g11 = $this->units;
				while($_g4 < $_g11->length) {
					$unit = $_g11[$_g4];
					++$_g4;
					if($unit->l >= 0 && $unit->r >= 0) {
						$this->local->setCell($x2, $this->row_mix_local->get($unit->l), $this->remote->getCell($x, $unit->r));
					} else {
						if($unit->p < 0 && $unit->r >= 0) {
							$this->local->setCell($x2, $this->row_mix_remote->get($unit->r), $this->remote->getCell($x, $unit->r));
						}
					}
					unset($unit);
				}
				unset($_g4,$_g11);
			}
			unset($x2);
		}
		if(null == $this->row_mix_remote) throw new HException('null iterable');
		$__hx__it = $this->row_mix_remote->keys();
		while($__hx__it->hasNext()) {
			unset($y);
			$y = $__hx__it->next();
			$y2 = $this->row_mix_remote->get($y);
			{
				$_g5 = 0;
				$_g12 = $this->column_units;
				while($_g5 < $_g12->length) {
					$unit1 = $_g12[$_g5];
					++$_g5;
					if($unit1->l >= 0 && $unit1->r >= 0) {
						$this->local->setCell($this->column_mix_local->get($unit1->l), $y2, $this->remote->getCell($unit1->r, $y));
					}
					unset($unit1);
				}
				unset($_g5,$_g12);
			}
			unset($y2);
		}
		return $this->conflicts;
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
	static function makeConflictedCell($view, $pcell, $lcell, $rcell) {
		return $view->toDatum("((( " . _hx_string_or_null($view->toString($pcell)) . " ))) " . _hx_string_or_null($view->toString($lcell)) . " /// " . _hx_string_or_null($view->toString($rcell)));
	}
	function __toString() { return 'coopy.Merger'; }
}
