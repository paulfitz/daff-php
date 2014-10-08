<?php

class Sys {
	public function __construct(){}
	static function args() {
		if(array_key_exists("argv", $_SERVER)) {
			return new _hx_array(array_slice($_SERVER["argv"], 1));
		} else {
			return (new _hx_array(array()));
		}
	}
	static function systemName() {
		$s = php_uname("s");
		$p = null;
		if(($p = _hx_index_of($s, " ", null)) >= 0) {
			return _hx_substr($s, 0, $p);
		} else {
			return $s;
		}
	}
	static function escapeArgument($arg) {
		$ok = true;
		{
			$_g1 = 0;
			$_g = strlen($arg);
			while($_g1 < $_g) {
				$i = $_g1++;
				$_g2 = _hx_char_code_at($arg, $i);
				if($_g2 !== null) {
					switch($_g2) {
					case 32:case 9:case 34:case 38:case 124:case 60:case 62:case 35:case 59:case 42:case 63:case 40:case 41:case 123:case 125:case 36:{
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
			return $arg;
		}
		return "\"" . _hx_string_or_null(_hx_explode("\"", _hx_explode("\\", $arg)->join("\\\\"))->join("\\\"")) . "\"";
	}
	static function command($cmd, $args = null) {
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
		if(Sys::systemName() === "Windows") {
			$cmd = "\"" . _hx_string_or_null($cmd) . "\"";
		}
		$result = 0;
		system($cmd, $result);
		return $result;
	}
	static function stdout() {
		return new sys_io_FileOutput(fopen("php://stdout", "w"));
	}
	static function stderr() {
		return new sys_io_FileOutput(fopen("php://stderr", "w"));
	}
	function __toString() { return 'Sys'; }
}
