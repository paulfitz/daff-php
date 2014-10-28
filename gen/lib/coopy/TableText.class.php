<?php

class coopy_TableText {
	public function __construct($tab) {
		if(!php_Boot::$skip_constructor) {
		$this->tab = $tab;
		$this->view = $tab->getCellView();
	}}
	public $tab;
	public $view;
	public function getCellText($x, $y) {
		return $this->view->toString($this->tab->getCell($x, $y));
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
	function __toString() { return 'coopy.TableText'; }
}
