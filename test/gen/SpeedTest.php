<?php

class harness_SpeedTest extends haxe_unit_TestCase {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public $data1;
	public $data2;
	public function setup() {
		$this->data1 = (new _hx_array(array()));
		$this->data2 = (new _hx_array(array()));
		$scale = 10000;
		{
			$_g = 0;
			while($_g < 2) {
				$k = $_g++;
				{
					$_g1 = 0;
					while($_g1 < $scale) {
						$i = $_g1++;
						$row = (new _hx_array(array()));
						$row->push("<supplier>");
						$row->push("<product_code>");
						$row->push("" . _hx_string_rec(($i + $k * 7), ""));
						$row->push("" . _hx_string_rec(_hx_mod(($i + $k * 7), 10), ""));
						$row->push("GBP");
						if($k === 1) {
							$this->data1->push($row);
						} else {
							$this->data2->push($row);
						}
						unset($row,$i);
					}
					unset($_g1);
				}
				unset($k);
			}
		}
		$this->data2 = $this->data1;
	}
	public function testMedium() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$flags = new coopy_CompareFlags();
		$flags->unchanged_column_context = 3;
		$align = coopy_Coopy::compareTables($table1, $table2, null)->align();
		$diff = harness_Native::table((new _hx_array(array())));
		$highlighter = new coopy_TableDiff($align, $flags);
		$flags->ordered = false;
		$highlighter->hilite($diff);
		$this->assertEquals(1, 1, _hx_anonymous(array("fileName" => "SpeedTest.hx", "lineNumber" => 41, "className" => "harness.SpeedTest", "methodName" => "testMedium")));
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
	function __toString() { return 'harness.SpeedTest'; }
}
