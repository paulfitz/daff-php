<?php

class coopy_SimpleMeta implements coopy_Meta{
	public function __construct($t, $has_properties = null) {
		if(!php_Boot::$skip_constructor) {
		if($has_properties === null) {
			$has_properties = true;
		}
		$this->t = $t;
		$this->rowChange();
		$this->colChange();
		$this->has_properties = $has_properties;
		$this->metadata = null;
		$this->keys = null;
		$this->row_active = false;
		$this->row_change_cache = null;
	}}
	public $t;
	public $name2row;
	public $name2col;
	public $has_properties;
	public $metadata;
	public $keys;
	public $row_active;
	public $row_change_cache;
	public function storeRowChanges($changes) {
		$this->row_change_cache = $changes;
		$this->row_active = true;
	}
	public function rowChange() {
		$this->name2row = null;
	}
	public function colChange() {
		$this->name2col = null;
	}
	public function col($key) {
		if($this->t->get_height() < 1) {
			return -1;
		}
		if($this->name2col === null) {
			$this->name2col = new haxe_ds_StringMap();
			$w = $this->t->get_width();
			{
				$_g = 0;
				while($_g < $w) {
					$c = $_g++;
					{
						$key1 = $this->t->getCell($c, 0);
						$this->name2col->set($key1, $c);
						unset($key1);
					}
					unset($c);
				}
			}
		}
		if(!$this->name2col->exists($key)) {
			return -1;
		}
		return $this->name2col->get($key);
	}
	public function row($key) {
		if($this->t->get_width() < 1) {
			return -1;
		}
		if($this->name2row === null) {
			$this->name2row = new haxe_ds_StringMap();
			$h = $this->t->get_height();
			{
				$_g = 1;
				while($_g < $h) {
					$r = $_g++;
					{
						$key1 = $this->t->getCell(0, $r);
						$this->name2row->set($key1, $r);
						unset($key1);
					}
					unset($r);
				}
			}
		}
		if(!$this->name2row->exists($key)) {
			return -1;
		}
		return $this->name2row->get($key);
	}
	public function alterColumns($columns) {
		$target = new haxe_ds_StringMap();
		$wfate = 0;
		if($this->has_properties) {
			$target->set("@", $wfate);
			$wfate++;
		}
		{
			$_g1 = 0;
			$_g = $columns->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$col = $columns[$i];
				if($col->prevName !== null) {
					$target->set($col->prevName, $wfate);
				}
				if($col->name !== null) {
					$wfate++;
				}
				unset($i,$col);
			}
		}
		$fate = new _hx_array(array());
		{
			$_g11 = 0;
			$_g2 = $this->t->get_width();
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$targeti = -1;
				$name = $this->t->getCell($i1, 0);
				if($target->exists($name)) {
					$targeti = $target->get($name);
				}
				$fate->push($targeti);
				unset($targeti,$name,$i1);
			}
		}
		$this->t->insertOrDeleteColumns($fate, $wfate);
		$start = null;
		if($this->has_properties) {
			$start = 1;
		} else {
			$start = 0;
		}
		$at = $start;
		{
			$_g12 = 0;
			$_g3 = $columns->length;
			while($_g12 < $_g3) {
				$i2 = $_g12++;
				$col1 = $columns[$i2];
				if($col1->name !== null) {
					if($col1->name !== $col1->prevName) {
						$this->t->setCell($at, 0, $col1->name);
					}
				}
				if($col1->name !== null) {
					$at++;
				}
				unset($i2,$col1);
			}
		}
		if(!$this->has_properties) {
			return true;
		}
		$this->colChange();
		$at = $start;
		{
			$_g13 = 0;
			$_g4 = $columns->length;
			while($_g13 < $_g4) {
				$i3 = $_g13++;
				$col2 = $columns[$i3];
				if($col2->name !== null) {
					$_g21 = 0;
					$_g31 = $col2->props;
					while($_g21 < $_g31->length) {
						$prop = $_g31[$_g21];
						++$_g21;
						$this->setCell($col2->name, $prop->name, $prop->val);
						unset($prop);
					}
					unset($_g31,$_g21);
				}
				if($col2->name !== null) {
					$at++;
				}
				unset($i3,$col2);
			}
		}
		return true;
	}
	public function setCell($c, $r, $val) {
		$ri = $this->row($r);
		if($ri === -1) {
			return false;
		}
		$ci = $this->col($c);
		if($ci === -1) {
			return false;
		}
		$this->t->setCell($ci, $ri, $val);
		return true;
	}
	public function addMetaData($column, $property, $val) {
		if($this->metadata === null) {
			$this->metadata = new haxe_ds_StringMap();
			$this->keys = new haxe_ds_StringMap();
		}
		if(!$this->metadata->exists($column)) {
			$value = new haxe_ds_StringMap();
			$this->metadata->set($column, $value);
		}
		$props = $this->metadata->get($column);
		{
			$value1 = $val;
			$props->set($property, $value1);
		}
		$this->keys->set($property, true);
	}
	public function asTable() {
		if($this->has_properties && $this->metadata === null) {
			return $this->t;
		}
		if($this->metadata === null) {
			return null;
		}
		$w = $this->t->get_width();
		$props = new _hx_array(array());
		if(null == $this->keys) throw new HException('null iterable');
		$__hx__it = $this->keys->keys();
		while($__hx__it->hasNext()) {
			unset($k);
			$k = $__hx__it->next();
			$props->push($k);
		}
		$props->sort((isset(Reflect::$compare) ? Reflect::$compare: array("Reflect", "compare")));
		$mt = new coopy_SimpleTable($w + 1, $props->length + 1);
		$mt->setCell(0, 0, "@");
		{
			$_g = 0;
			while($_g < $w) {
				$x = $_g++;
				$name = $this->t->getCell($x, 0);
				$mt->setCell(1 + $x, 0, $name);
				if(!$this->metadata->exists($name)) {
					continue;
				}
				$vals = $this->metadata->get($name);
				{
					$_g2 = 0;
					$_g1 = $props->length;
					while($_g2 < $_g1) {
						$i = $_g2++;
						if($vals->exists($props[$i])) {
							$mt->setCell(1 + $x, $i + 1, $vals->get($props[$i]));
						}
						unset($i);
					}
					unset($_g2,$_g1);
				}
				unset($x,$vals,$name);
			}
		}
		{
			$_g11 = 0;
			$_g3 = $props->length;
			while($_g11 < $_g3) {
				$y = $_g11++;
				$mt->setCell(0, $y + 1, $props[$y]);
				unset($y);
			}
		}
		return $mt;
	}
	public function cloneMeta($table = null) {
		$result = new coopy_SimpleMeta($table, null);
		if($this->metadata !== null) {
			$result->keys = new haxe_ds_StringMap();
			if(null == $this->keys) throw new HException('null iterable');
			$__hx__it = $this->keys->keys();
			while($__hx__it->hasNext()) {
				unset($k);
				$k = $__hx__it->next();
				$result->keys->set($k, true);
			}
			$result->metadata = new haxe_ds_StringMap();
			if(null == $this->metadata) throw new HException('null iterable');
			$__hx__it = $this->metadata->keys();
			while($__hx__it->hasNext()) {
				unset($k1);
				$k1 = $__hx__it->next();
				if(!$this->metadata->exists($k1)) {
					continue;
				}
				$vals = $this->metadata->get($k1);
				$nvals = new haxe_ds_StringMap();
				if(null == $vals) throw new HException('null iterable');
				$__hx__it2 = $vals->keys();
				while($__hx__it2->hasNext()) {
					unset($p);
					$p = $__hx__it2->next();
					$value = $vals->get($p);
					$nvals->set($p, $value);
					unset($value);
				}
				$result->metadata->set($k1, $nvals);
				unset($vals,$nvals);
			}
		}
		return $result;
	}
	public function useForColumnChanges() {
		return true;
	}
	public function useForRowChanges() {
		return $this->row_active;
	}
	public function changeRow($rc) {
		$this->row_change_cache->push($rc);
		return false;
	}
	public function applyFlags($flags) {
		return false;
	}
	public function getRowStream() {
		return new coopy_TableStream($this->t);
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
	function __toString() { return 'coopy.SimpleMeta'; }
}
