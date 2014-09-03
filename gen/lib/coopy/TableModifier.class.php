<?php

class coopy_TableModifier {
	public function __construct($t) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.TableModifier::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->t = $t;
		$GLOBALS['%s']->pop();
	}}
	public $t;
	public function removeColumn($at) {
		$GLOBALS['%s']->push("coopy.TableModifier::removeColumn");
		$__hx__spos = $GLOBALS['%s']->length;
		$fate = (new _hx_array(array()));
		{
			$_g1 = 0;
			$_g = $this->t->get_width();
			while($_g1 < $_g) {
				$i = $_g1++;
				if($i < $at) {
					$fate->push($i);
				} else {
					if($i > $at) {
						$fate->push($i - 1);
					} else {
						$fate->push(-1);
					}
				}
				unset($i);
			}
		}
		{
			$tmp = $this->t->insertOrDeleteColumns($fate, $this->t->get_width() - 1);
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
	function __toString() { return 'coopy.TableModifier'; }
}
