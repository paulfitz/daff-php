<?php

class harness_MergeTest extends haxe_unit_TestCase {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public $data1;
	public $data2;
	public $data3;
	public $data4;
	public $data3b;
	public $data4b;
	public function setup() {
		$this->data1 = (new _hx_array(array((new _hx_array(array("Country", "Capital"))), (new _hx_array(array("Ireland", "Dublin"))), (new _hx_array(array("France", "Paris"))), (new _hx_array(array("Spain", "Barcelona"))))));
		$this->data2 = (new _hx_array(array((new _hx_array(array("Country", "Code", "Capital"))), (new _hx_array(array("Ireland", "ie", "Dublin"))), (new _hx_array(array("France", "fr", "Paris"))), (new _hx_array(array("Spain", "es", "Madrid"))), (new _hx_array(array("Germany", "de", "Berlin"))))));
		$this->data3 = (new _hx_array(array((new _hx_array(array("Country", "Capital"))), (new _hx_array(array("Dear old Ireland", "Dublin"))), (new _hx_array(array("Spain", "Barcelona"))), (new _hx_array(array("Finland", "Helsinki"))))));
		$this->data4 = (new _hx_array(array((new _hx_array(array("Country", "Code", "Capital"))), (new _hx_array(array("Dear old Ireland", "ie", "Dublin"))), (new _hx_array(array("Spain", "es", "Madrid"))), (new _hx_array(array("Finland", null, "Helsinki"))), (new _hx_array(array("Germany", "de", "Berlin"))))));
		$this->data3b = (new _hx_array(array((new _hx_array(array("Country", "Capital"))), (new _hx_array(array("Dear old Ireland", "Dublin"))), (new _hx_array(array("Spain", "Lisbon"))), (new _hx_array(array("Finland", "Helsinki"))))));
		$this->data4b = (new _hx_array(array((new _hx_array(array("Country", "Code", "Capital"))), (new _hx_array(array("Dear old Ireland", "ie", "Dublin"))), (new _hx_array(array("Spain", "es", "((( Barcelona ))) Madrid /// Lisbon"))), (new _hx_array(array("Finland", null, "Helsinki"))), (new _hx_array(array("Germany", "de", "Berlin"))))));
	}
	public function testUnconflicted() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$table3 = harness_Native::table($this->data3);
		$table4 = harness_Native::table($this->data4);
		$flags = new coopy_CompareFlags();
		$merger = new coopy_Merger($table1, $table2, $table3, $flags);
		$conflicts = $merger->apply();
		$this->assertEquals($conflicts, 0, _hx_anonymous(array("fileName" => "MergeTest.hx", "lineNumber" => 51, "className" => "harness.MergeTest", "methodName" => "testUnconflicted")));
		$this->assertEquals(coopy_SimpleTable::tableToString($table2), coopy_SimpleTable::tableToString($table4), _hx_anonymous(array("fileName" => "MergeTest.hx", "lineNumber" => 52, "className" => "harness.MergeTest", "methodName" => "testUnconflicted")));
	}
	public function testConflicted() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$table3 = harness_Native::table($this->data3b);
		$table4 = harness_Native::table($this->data4b);
		$flags = new coopy_CompareFlags();
		$merger = new coopy_Merger($table1, $table2, $table3, $flags);
		$conflicts = $merger->apply();
		$this->assertEquals($conflicts, 1, _hx_anonymous(array("fileName" => "MergeTest.hx", "lineNumber" => 64, "className" => "harness.MergeTest", "methodName" => "testConflicted")));
		$this->assertEquals(coopy_SimpleTable::tableToString($table2), coopy_SimpleTable::tableToString($table4), _hx_anonymous(array("fileName" => "MergeTest.hx", "lineNumber" => 65, "className" => "harness.MergeTest", "methodName" => "testConflicted")));
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
	function __toString() { return 'harness.MergeTest'; }
}
