<?php

class coopy_Change {
	public function __construct($txt = null) {
		if(!php_Boot::$skip_constructor) {
		if($txt !== null) {
			$this->mode = coopy_ChangeType::$NOTE_CHANGE;
			$this->change = $txt;
		} else {
			$this->mode = coopy_ChangeType::$NO_CHANGE;
		}
	}}
	public $change;
	public $parent;
	public $local;
	public $remote;
	public $mode;
	public function getMode() {
		return "" . Std::string($this->mode);
	}
	public function toString() {
		$_g = $this->mode;
		switch($_g->index) {
		case 0:{
			return "no change";
		}break;
		case 2:{
			return "local change: " . Std::string($this->remote) . " -> " . Std::string($this->local);
		}break;
		case 1:{
			return "remote change: " . Std::string($this->local) . " -> " . Std::string($this->remote);
		}break;
		case 3:{
			return "conflicting change: " . Std::string($this->parent) . " -> " . Std::string($this->local) . " / " . Std::string($this->remote);
		}break;
		case 4:{
			return "same change: " . Std::string($this->parent) . " -> " . Std::string($this->local) . " / " . Std::string($this->remote);
		}break;
		case 5:{
			return $this->change;
		}break;
		}
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
