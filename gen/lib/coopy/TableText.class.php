<?php

class coopy_TableText {
	public function __construct($rows) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.TableText::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->rows = $rows;
		$this->view = $rows->getCellView();
		$GLOBALS['%s']->pop();
	}}
	public $rows;
	public $view;
	public function getCellText($x, $y) {
		$GLOBALS['%s']->push("coopy.TableText::getCellText");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->view->toString($this->rows->getCell($x, $y));
			$GLOBALS['%s']->pop();
			return $tmp;
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
	function __toString() { return 'coopy.TableText'; }
}
