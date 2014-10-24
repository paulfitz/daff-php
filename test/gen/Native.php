<?php

class harness_Native {
	public function __construct(){}
	static function hlist($data) {
		$ndata = array();
		$w = _hx_len($data);
		if($w > 0) {
			$ndata = array_pad(array(),$w,null);
			{
				$_g = 0;
				while($_g < $w) {
					$j = $_g++;
					$x = $data[$j];
					$ndata[$j] = $x;
					unset($x,$j);
				}
			}
		}
		return $ndata;
	}
	static function nativeArray($data) {
		$ndata = array();
		$h = _hx_len($data);
		if($h > 0) {
			$w = _hx_len($data[0]);
			$ndata = array_pad(array(),$h,array_pad(array(),$w,null));
			{
				$_g = 0;
				while($_g < $h) {
					$i = $_g++;
					{
						$_g1 = 0;
						while($_g1 < $w) {
							$j = $_g1++;
							$x = $data[$i][$j];
							$ndata[$i][$j] = $x;
							unset($x,$j);
						}
						unset($_g1);
					}
					unset($i);
				}
			}
		}
		return $ndata;
	}
	static function table($data) {
		$data = harness_Native::nativeArray($data);
		return new coopy_PhpTableView($data);
	}
	static function isList($v) {
		$keys = array_keys($v);
		return array_keys($keys) === $keys;
	}
	static function row($v, $r) {
		return $v[$r];
	}
	static function hexit($v) {
		Sys::hexit($v);
	}
	function __toString() { return 'harness.Native'; }
}
