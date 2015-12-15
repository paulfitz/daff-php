<?php

class coopy_SimpleTable implements coopy_Table{
	public function __construct($w, $h) {
		if(!php_Boot::$skip_constructor) {
		$this->data = new haxe_ds_IntMap();
		$this->w = $w;
		$this->h = $h;
		$this->meta = null;
	}}
	public $data;
	public $w;
	public $h;
	public $meta;
	public function getTable() {
		return $this;
	}
	public function get_width() {
		return $this->w;
	}
	public function get_height() {
		return $this->h;
	}
	public function getCell($x, $y) {
		return $this->data->get($x + $y * $this->w);
	}
	public function setCell($x, $y, $c) {
		$value = $c;
		$this->data->set($x + $y * $this->w, $value);
	}
	public function toString() {
		return coopy_SimpleTable::tableToString($this);
	}
	public function getCellView() {
		return new coopy_SimpleView();
	}
	public function isResizable() {
		return true;
	}
	public function resize($w, $h) {
		$this->w = $w;
		$this->h = $h;
		return true;
	}
	public function clear() {
		$this->data = new haxe_ds_IntMap();
	}
	public function insertOrDeleteRows($fate, $hfate) {
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
		return true;
	}
	public function insertOrDeleteColumns($fate, $wfate) {
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
		return true;
	}
	public function trimBlank() {
		if($this->h === 0) {
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
		return true;
	}
	public function getData() {
		return null;
	}
	public function hclone() {
		$result = new coopy_SimpleTable($this->get_width(), $this->get_height());
		{
			$_g1 = 0;
			$_g = $this->get_height();
			while($_g1 < $_g) {
				$i = $_g1++;
				{
					$_g3 = 0;
					$_g2 = $this->get_width();
					while($_g3 < $_g2) {
						$j = $_g3++;
						$result->setCell($j, $i, $this->getCell($j, $i));
						unset($j);
					}
					unset($_g3,$_g2);
				}
				unset($i);
			}
		}
		if($this->meta !== null) {
			$result->meta = $this->meta->cloneMeta($result);
		}
		return $result;
	}
	public function setMeta($meta) {
		$this->meta = $meta;
	}
	public function getMeta() {
		return $this->meta;
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
		$meta = $tab->getMeta();
		if($meta !== null) {
			$stream = $meta->getRowStream();
			if($stream !== null) {
				$x = "";
				$cols = $stream->fetchColumns();
				{
					$_g1 = 0;
					$_g = $cols->length;
					while($_g1 < $_g) {
						$i = $_g1++;
						if($i > 0) {
							$x .= ",";
						}
						$x .= _hx_string_or_null($cols[$i]);
						unset($i);
					}
				}
				$x .= "\x0A";
				$row = $stream->fetchRow();
				while($row !== null) {
					{
						$_g11 = 0;
						$_g2 = $cols->length;
						while($_g11 < $_g2) {
							$i1 = $_g11++;
							if($i1 > 0) {
								$x .= ",";
							}
							$x .= Std::string($row->get($cols[$i1]));
							unset($i1);
						}
						unset($_g2,$_g11);
					}
					$x .= "\x0A";
					$row = $stream->fetchRow();
				}
				return $x;
			}
		}
		$x1 = "";
		{
			$_g12 = 0;
			$_g3 = $tab->get_height();
			while($_g12 < $_g3) {
				$i2 = $_g12++;
				{
					$_g31 = 0;
					$_g21 = $tab->get_width();
					while($_g31 < $_g21) {
						$j = $_g31++;
						if($j > 0) {
							$x1 .= ",";
						}
						$x1 .= Std::string($tab->getCell($j, $i2));
						unset($j);
					}
					unset($_g31,$_g21);
				}
				$x1 .= "\x0A";
				unset($i2);
			}
		}
		return $x1;
	}
	static function tableIsSimilar($tab1, $tab2) {
		if($tab1->get_height() === -1 || $tab2->get_height() === -1) {
			$txt1 = coopy_SimpleTable::tableToString($tab1);
			$txt2 = coopy_SimpleTable::tableToString($tab2);
			return $txt1 === $txt2;
		}
		if($tab1->get_width() !== $tab2->get_width()) {
			return false;
		}
		if($tab1->get_height() !== $tab2->get_height()) {
			return false;
		}
		$v = $tab1->getCellView();
		{
			$_g1 = 0;
			$_g = $tab1->get_height();
			while($_g1 < $_g) {
				$i = $_g1++;
				{
					$_g3 = 0;
					$_g2 = $tab1->get_width();
					while($_g3 < $_g2) {
						$j = $_g3++;
						if(!$v->equals($tab1->getCell($j, $i), $tab2->getCell($j, $i))) {
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
	static $__properties__ = array("get_width" => "get_width","get_height" => "get_height");
	function __toString() { return $this->toString(); }
}
