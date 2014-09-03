<?php

class coopy_Change {
	public function __construct($txt = null) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.Change::new");
		$__hx__spos = $GLOBALS['%s']->length;
		if($txt !== null) {
			$this->mode = coopy_ChangeType::$NOTE_CHANGE;
			$this->change = $txt;
		} else {
			$this->mode = coopy_ChangeType::$NO_CHANGE;
		}
		$GLOBALS['%s']->pop();
	}}
	public $change;
	public $parent;
	public $local;
	public $remote;
	public $mode;
	public function getMode() {
		$GLOBALS['%s']->push("coopy.Change::getMode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = "" . Std::string($this->mode);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function toString() {
		$GLOBALS['%s']->push("coopy.Change::toString");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g = $this->mode;
		switch($_g->index) {
		case 0:{
			$GLOBALS['%s']->pop();
			return "no change";
		}break;
		case 2:{
			$tmp = "local change: " . Std::string($this->remote) . " -> " . Std::string($this->local);
			$GLOBALS['%s']->pop();
			return $tmp;
		}break;
		case 1:{
			$tmp = "remote change: " . Std::string($this->local) . " -> " . Std::string($this->remote);
			$GLOBALS['%s']->pop();
			return $tmp;
		}break;
		case 3:{
			$tmp = "conflicting change: " . Std::string($this->parent) . " -> " . Std::string($this->local) . " / " . Std::string($this->remote);
			$GLOBALS['%s']->pop();
			return $tmp;
		}break;
		case 4:{
			$tmp = "same change: " . Std::string($this->parent) . " -> " . Std::string($this->local) . " / " . Std::string($this->remote);
			$GLOBALS['%s']->pop();
			return $tmp;
		}break;
		case 5:{
			$tmp = $this->change;
			$GLOBALS['%s']->pop();
			return $tmp;
		}break;
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
	function __toString() { return $this->toString(); }
}
