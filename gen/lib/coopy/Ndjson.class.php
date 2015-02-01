<?php

class coopy_Ndjson {
	public function __construct($tab) {
		if(!php_Boot::$skip_constructor) {
		$this->tab = $tab;
		$this->view = $tab->getCellView();
		$this->header_row = 0;
	}}
	public $tab;
	public $view;
	public $columns;
	public $header_row;
	public function renderRow($r) {
		$row = new haxe_ds_StringMap();
		{
			$_g1 = 0;
			$_g = $this->tab->get_width();
			while($_g1 < $_g) {
				$c = $_g1++;
				$key = $this->view->toString($this->tab->getCell($c, $this->header_row));
				if($c === 0 && $this->header_row === 1) {
					$key = "@:@";
				}
				{
					$value = $this->tab->getCell($c, $r);
					$row->set($key, $value);
					unset($value);
				}
				unset($key,$c);
			}
		}
		return haxe_format_JsonPrinter::hprint($row, null, null);
	}
	public function render() {
		$txt = "";
		$offset = 0;
		if($this->tab->get_height() === 0) {
			return $txt;
		}
		if($this->tab->get_width() === 0) {
			return $txt;
		}
		if(_hx_equal($this->tab->getCell(0, 0), "@:@")) {
			$offset = 1;
		}
		$this->header_row = $offset;
		{
			$_g1 = $this->header_row + 1;
			$_g = $this->tab->get_height();
			while($_g1 < $_g) {
				$r = $_g1++;
				$txt .= _hx_string_or_null($this->renderRow($r));
				$txt .= "\x0A";
				unset($r);
			}
		}
		return $txt;
	}
	public function addRow($r, $txt) {
		$json = _hx_deref(new haxe_format_JsonParser($txt))->parseRec();
		if($this->columns === null) {
			$this->columns = new haxe_ds_StringMap();
		}
		$w = $this->tab->get_width();
		$h = $this->tab->get_height();
		$resize = false;
		{
			$_g = 0;
			$_g1 = Reflect::fields($json);
			while($_g < $_g1->length) {
				$name = $_g1[$_g];
				++$_g;
				if(!$this->columns->exists($name)) {
					$this->columns->set($name, $w);
					$w++;
					$resize = true;
				}
				unset($name);
			}
		}
		if($r >= $h) {
			$h = $r + 1;
			$resize = true;
		}
		if($resize) {
			$this->tab->resize($w, $h);
		}
		{
			$_g2 = 0;
			$_g11 = Reflect::fields($json);
			while($_g2 < $_g11->length) {
				$name1 = $_g11[$_g2];
				++$_g2;
				$v = Reflect::field($json, $name1);
				$c = $this->columns->get($name1);
				$this->tab->setCell($c, $r, $v);
				unset($v,$name1,$c);
			}
		}
	}
	public function addHeaderRow($r) {
		$names = $this->columns->keys();
		$__hx__it = $names;
		while($__hx__it->hasNext()) {
			unset($n);
			$n = $__hx__it->next();
			$this->tab->setCell($this->columns->get($n), $r, $this->view->toDatum($n));
		}
	}
	public function parse($txt) {
		$this->columns = null;
		$rows = _hx_explode("\x0A", $txt);
		$h = $rows->length;
		if($h === 0) {
			$this->tab->clear();
			return;
		}
		if($rows[$h - 1] === "") {
			$h--;
		}
		{
			$_g = 0;
			while($_g < $h) {
				$i = $_g++;
				$at = $h - $i - 1;
				$this->addRow($at + 1, $rows[$at]);
				unset($i,$at);
			}
		}
		$this->addHeaderRow(0);
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
	function __toString() { return 'coopy.Ndjson'; }
}
