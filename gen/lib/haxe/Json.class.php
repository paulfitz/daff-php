<?php

class haxe_Json {
	public function __construct(){}
	static function phpJsonDecode($json) {
		$GLOBALS['%s']->push("haxe.Json::phpJsonDecode");
		$__hx__spos = $GLOBALS['%s']->length;
		$val = json_decode($json);
		{
			$tmp = haxe_Json::convertAfterDecode($val);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static function convertAfterDecode($val) {
		$GLOBALS['%s']->push("haxe.Json::convertAfterDecode");
		$__hx__spos = $GLOBALS['%s']->length;
		$arr = null;
		if(is_object($val)) {
			{
				$arr1 = php_Lib::associativeArrayOfObject($val);
				$arr = array_map((isset(haxe_Json::$convertAfterDecode) ? haxe_Json::$convertAfterDecode: array("haxe_Json", "convertAfterDecode")), $arr1);
			}
			{
				$tmp = _hx_anonymous($arr);
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		} else {
			if(is_array($val)) {
				{
					$arr2 = $val;
					$arr = array_map((isset(haxe_Json::$convertAfterDecode) ? haxe_Json::$convertAfterDecode: array("haxe_Json", "convertAfterDecode")), $arr2);
				}
				{
					$tmp = new _hx_array($arr);
					$GLOBALS['%s']->pop();
					return $tmp;
				}
			} else {
				$GLOBALS['%s']->pop();
				return $val;
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function phpJsonEncode($val, $replacer = null, $space = null) {
		$GLOBALS['%s']->push("haxe.Json::phpJsonEncode");
		$__hx__spos = $GLOBALS['%s']->length;
		if(null !== $replacer || null !== $space) {
			$tmp = haxe_format_JsonPrinter::hprint($val, $replacer, $space);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$json = json_encode(haxe_Json::convertBeforeEncode($val));
		if(($json === false)) {
			throw new HException("invalid json");
		} else {
			$GLOBALS['%s']->pop();
			return $json;
		}
		$GLOBALS['%s']->pop();
	}
	static function convertBeforeEncode($val) {
		$GLOBALS['%s']->push("haxe.Json::convertBeforeEncode");
		$__hx__spos = $GLOBALS['%s']->length;
		$arr = null;
		if(is_object($val)) {
			$_g = get_class($val);
			switch($_g) {
			case "_hx_anonymous":case "stdClass":{
				$arr = php_Lib::associativeArrayOfObject($val);
			}break;
			case "_hx_array":{
				$arr = php_Lib::toPhpArray($val);
			}break;
			case "Date":{
				$tmp = Std::string($val);
				$GLOBALS['%s']->pop();
				return $tmp;
			}break;
			case "HList":{
				$arr = php_Lib::toPhpArray(Lambda::harray($val));
			}break;
			case "_hx_enum":{
				$e = $val;
				{
					$tmp = $e->index;
					$GLOBALS['%s']->pop();
					return $tmp;
				}
			}break;
			case "StringMap":case "IntMap":{
				$arr = php_Lib::associativeArrayOfHash($val);
			}break;
			default:{
				$arr = php_Lib::associativeArrayOfObject($val);
			}break;
			}
		} else {
			if(is_array($val)) {
				$arr = $val;
			} else {
				if(is_float($val) && !is_finite($val)) {
					$val = null;
				}
				{
					$GLOBALS['%s']->pop();
					return $val;
				}
			}
		}
		{
			$tmp = array_map((isset(haxe_Json::$convertBeforeEncode) ? haxe_Json::$convertBeforeEncode: array("haxe_Json", "convertBeforeEncode")), $arr);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'haxe.Json'; }
}
