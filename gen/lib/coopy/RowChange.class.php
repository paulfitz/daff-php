<?php

class coopy_RowChange {
	public function __construct() {}
	public $cond;
	public $val;
	public $conflicting_val;
	public $conflicting_parent_val;
	public $conflicted;
	public $is_key;
	public $action;
	public function showMap($m) {
		if(!php_Boot::$skip_constructor) {
		if($m === null) {
			return "{}";
		}
		$txt = "";
		if(null == $m) throw new HException('null iterable');
		$__hx__it = $m->keys();
		while($__hx__it->hasNext()) {
			unset($k);
			$k = $__hx__it->next();
			if($txt !== "") {
				$txt .= ", ";
			}
			$v = $m->get($k);
			$txt .= _hx_string_or_null($k) . "=" . Std::string($v);
			unset($v);
		}
		return "{ " . _hx_string_or_null($txt) . " }";
	}}
	public function toString() {
		return _hx_string_or_null($this->action) . " " . _hx_string_or_null($this->showMap($this->cond)) . " : " . _hx_string_or_null($this->showMap($this->val));
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
