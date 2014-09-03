<?php

class Sys {
	public function __construct(){}
	static function args() {
		$GLOBALS['%s']->push("Sys::args");
		$__hx__spos = $GLOBALS['%s']->length;
		if(array_key_exists("argv", $_SERVER)) {
			$tmp = new _hx_array(array_slice($_SERVER["argv"], 1));
			$GLOBALS['%s']->pop();
			return $tmp;
		} else {
			$tmp = (new _hx_array(array()));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function escapeArgument($arg) {
		$GLOBALS['%s']->push("Sys::escapeArgument");
		$__hx__spos = $GLOBALS['%s']->length;
		$ok = true;
		{
			$_g1 = 0;
			$_g = strlen($arg);
			while($_g1 < $_g) {
				$i = $_g1++;
				$_g2 = _hx_char_code_at($arg, $i);
				if($_g2 !== null) {
					switch($_g2) {
					case 32:case 34:{
						$ok = false;
					}break;
					case 0:case 13:case 10:{
						$arg = _hx_substr($arg, 0, $i);
					}break;
					}
				}
				unset($i,$_g2);
			}
		}
		if($ok) {
			$GLOBALS['%s']->pop();
			return $arg;
		}
		{
			$tmp = "\"" . _hx_string_or_null(_hx_explode("\"", $arg)->join("\\\"")) . "\"";
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function command($cmd, $args = null) {
		$GLOBALS['%s']->push("Sys::command");
		$__hx__spos = $GLOBALS['%s']->length;
		if($args !== null) {
			$cmd = Sys::escapeArgument($cmd);
			{
				$_g = 0;
				while($_g < $args->length) {
					$a = $args[$_g];
					++$_g;
					$cmd .= " " . _hx_string_or_null(Sys::escapeArgument($a));
					unset($a);
				}
			}
		}
		$result = 0;
		system($cmd, $result);
		{
			$GLOBALS['%s']->pop();
			return $result;
		}
		$GLOBALS['%s']->pop();
	}
	static function stdout() {
		$GLOBALS['%s']->push("Sys::stdout");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new sys_io_FileOutput(fopen("php://stdout", "w"));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function stderr() {
		$GLOBALS['%s']->push("Sys::stderr");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = new sys_io_FileOutput(fopen("php://stderr", "w"));
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'Sys'; }
}
