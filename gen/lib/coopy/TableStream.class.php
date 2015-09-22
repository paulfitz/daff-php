<?php

class coopy_TableStream implements coopy_RowStream{
	public function __construct($t) {
		if(!php_Boot::$skip_constructor) {
		$this->t = $t;
		$this->at = -1;
		$this->h = $t->get_height();
		$this->src = null;
		if($this->h < 0) {
			$meta = $t->getMeta();
			if($meta === null) {
				throw new HException("Cannot get meta information for table");
			}
			$this->src = $meta->getRowStream();
			if($this->src === null) {
				throw new HException("Cannot iterate table");
			}
		}
	}}
	public $t;
	public $at;
	public $h;
	public $src;
	public $columns;
	public $row;
	public function fetchColumns() {
		if($this->columns !== null) {
			return $this->columns;
		}
		if($this->src !== null) {
			$this->columns = $this->src->fetchColumns();
			return $this->columns;
		}
		$this->columns = new _hx_array(array());
		{
			$_g1 = 0;
			$_g = $this->t->get_width();
			while($_g1 < $_g) {
				$i = $_g1++;
				$this->columns->push($this->t->getCell($i, 0));
				unset($i);
			}
		}
		return $this->columns;
	}
	public function fetchRow() {
		if($this->src !== null) {
			return $this->src->fetchRow();
		}
		if($this->at >= $this->h) {
			return null;
		}
		$row = new haxe_ds_StringMap();
		{
			$_g1 = 0;
			$_g = $this->columns->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				{
					$v = $this->t->getCell($i, $this->at);
					$row->set($this->columns[$i], $v);
					$v;
					unset($v);
				}
				unset($i);
			}
		}
		return $row;
	}
	public function fetch() {
		if($this->at === -1) {
			$this->at++;
			if($this->src !== null) {
				$this->fetchColumns();
			}
			return true;
		}
		if($this->src !== null) {
			$this->at = 1;
			$this->row = $this->fetchRow();
			return $this->row !== null;
		}
		$this->at++;
		return $this->at < $this->h;
	}
	public function getCell($x) {
		if($this->at === 0) {
			return $this->columns[$x];
		}
		if($this->row !== null) {
			return $this->row->get($this->columns[$x]);
		}
		return $this->t->getCell($x, $this->at);
	}
	public function width() {
		$this->fetchColumns();
		return $this->columns->length;
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
	function __toString() { return 'coopy.TableStream'; }
}
