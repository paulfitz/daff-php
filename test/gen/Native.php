<?php

class harness_Native {
	public function __construct(){}
	static function nativeArray($data) {
		$ndata = array();
		$h = _hx_len($data);
		if($h > 0) {
			$w = _hx_len($data[0]);
			{
				$_g = 0;
				while($_g < $h) {
					$i = $_g++;
					$row = $data[$i];
					$nrow = array();
					{
						$_g1 = 0;
						while($_g1 < $w) {
							$j = $_g1++;
							$x = $row[$j];
							array_push($nrow,$x);
							unset($x,$j);
						}
						unset($_g1);
					}
					array_push($ndata,$nrow);
					unset($row,$i);
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
