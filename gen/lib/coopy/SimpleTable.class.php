<?php

class coopy_SimpleTable implements coopy_Table{
	public function __construct($w, $h) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.SimpleTable::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->data = new haxe_ds_IntMap();
		$this->w = $w;
		$this->h = $h;
		$GLOBALS['%s']->pop();
	}}
	public $data;
	public $w;
	public $h;
	public function getTable() {
		$GLOBALS['%s']->push("coopy.SimpleTable::getTable");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return $this;
		}
		$GLOBALS['%s']->pop();
	}
	public function get_width() {
		$GLOBALS['%s']->push("coopy.SimpleTable::get_width");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->w;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function get_height() {
		$GLOBALS['%s']->push("coopy.SimpleTable::get_height");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->h;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function get_size() {
		$GLOBALS['%s']->push("coopy.SimpleTable::get_size");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->h;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getCell($x, $y) {
		$GLOBALS['%s']->push("coopy.SimpleTable::getCell");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->data->get($x + $y * $this->w);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function setCell($x, $y, $c) {
		$GLOBALS['%s']->push("coopy.SimpleTable::setCell");
		$__hx__spos = $GLOBALS['%s']->length;
		$value = $c;
		$this->data->set($x + $y * $this->w, $value);
		$GLOBALS['%s']->pop();
	}
	public function toString() {
		$GLOBALS['%s']->push("coopy.SimpleTable::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = coopy_SimpleTable::tableToString($this);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getCellView() {
		$GLOBALS['%s']->push("coopy.SimpleTable::getCellView");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new coopy_SimpleView();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function isResizable() {
		$GLOBALS['%s']->push("coopy.SimpleTable::isResizable");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	public function resize($w, $h) {
		$GLOBALS['%s']->push("coopy.SimpleTable::resize");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->w = $w;
		$this->h = $h;
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	public function clear() {
		$GLOBALS['%s']->push("coopy.SimpleTable::clear");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->data = new haxe_ds_IntMap();
		$GLOBALS['%s']->pop();
	}
	public function insertOrDeleteRows($fate, $hfate) {
		$GLOBALS['%s']->push("coopy.SimpleTable::insertOrDeleteRows");
		$__hx__spos = $GLOBALS['%s']->length;
		$data2 = new haxe_ds_IntMap();
		{
			$_g1 = 0;
			$_g = $fate->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$j = $fate[$i];
				if($j !== -1) {
					$_g3 = 0;
					$_g2 = $this->w;
					while($_g3 < $_g2) {
						$c = $_g3++;
						$idx = $i * $this->w + $c;
						if($this->data->exists($idx)) {
							$value = $this->data->get($idx);
							$data2->set($j * $this->w + $c, $value);
							unset($value);
						}
						unset($idx,$c);
					}
					unset($_g3,$_g2);
				}
				unset($j,$i);
			}
		}
		$this->h = $hfate;
		$this->data = $data2;
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	public function insertOrDeleteColumns($fate, $wfate) {
		$GLOBALS['%s']->push("coopy.SimpleTable::insertOrDeleteColumns");
		$__hx__spos = $GLOBALS['%s']->length;
		$data2 = new haxe_ds_IntMap();
		{
			$_g1 = 0;
			$_g = $fate->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$j = $fate[$i];
				if($j !== -1) {
					$_g3 = 0;
					$_g2 = $this->h;
					while($_g3 < $_g2) {
						$r = $_g3++;
						$idx = $r * $this->w + $i;
						if($this->data->exists($idx)) {
							$value = $this->data->get($idx);
							$data2->set($r * $wfate + $j, $value);
							unset($value);
						}
						unset($r,$idx);
					}
					unset($_g3,$_g2);
				}
				unset($j,$i);
			}
		}
		$this->w = $wfate;
		$this->data = $data2;
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	public function trimBlank() {
		$GLOBALS['%s']->push("coopy.SimpleTable::trimBlank");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->h === 0) {
			$GLOBALS['%s']->pop();
			return true;
		}
		$h_test = $this->h;
		if($h_test >= 3) {
			$h_test = 3;
		}
		$view = $this->getCellView();
		$space = $view->toDatum("");
		$more = true;
		while($more) {
			{
				$_g1 = 0;
				$_g = $this->get_width();
				while($_g1 < $_g) {
					$i = $_g1++;
					$c = $this->getCell($i, $this->h - 1);
					if(!($view->equals($c, $space) || $c === null)) {
						$more = false;
						break;
					}
					unset($i,$c);
				}
				unset($_g1,$_g);
			}
			if($more) {
				$this->h--;
			}
		}
		$more = true;
		$nw = $this->w;
		while($more) {
			if($this->w === 0) {
				break;
			}
			{
				$_g2 = 0;
				while($_g2 < $h_test) {
					$i1 = $_g2++;
					$c1 = $this->getCell($nw - 1, $i1);
					if(!($view->equals($c1, $space) || $c1 === null)) {
						$more = false;
						break;
					}
					unset($i1,$c1);
				}
				unset($_g2);
			}
			if($more) {
				$nw--;
			}
		}
		if($nw === $this->w) {
			$GLOBALS['%s']->pop();
			return true;
		}
		$data2 = new haxe_ds_IntMap();
		{
			$_g3 = 0;
			while($_g3 < $nw) {
				$i2 = $_g3++;
				{
					$_g21 = 0;
					$_g11 = $this->h;
					while($_g21 < $_g11) {
						$r = $_g21++;
						$idx = $r * $this->w + $i2;
						if($this->data->exists($idx)) {
							$value = $this->data->get($idx);
							$data2->set($r * $nw + $i2, $value);
							unset($value);
						}
						unset($r,$idx);
					}
					unset($_g21,$_g11);
				}
				unset($i2);
			}
		}
		$this->w = $nw;
		$this->data = $data2;
		{
			$GLOBALS['%s']->pop();
			return true;
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
	static function tableToString($tab) {
		$GLOBALS['%s']->push("coopy.SimpleTable::tableToString");
		$__hx__spos = $GLOBALS['%s']->length;
		$x = "";
		{
			$_g1 = 0;
			$_g = $tab->get_height();
			while($_g1 < $_g) {
				$i = $_g1++;
				{
					$_g3 = 0;
					$_g2 = $tab->get_width();
					while($_g3 < $_g2) {
						$j = $_g3++;
						if($j > 0) {
							$x .= " ";
						}
						$x .= Std::string($tab->getCell($j, $i));
						unset($j);
					}
					unset($_g3,$_g2);
				}
				$x .= "\x0A";
				unset($i);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $x;
		}
		$GLOBALS['%s']->pop();
	}
	static $__properties__ = array("get_size" => "get_size","get_width" => "get_width","get_height" => "get_height");
	function __toString() { return $this->toString(); }
}
