<?php

class harness_TypeTest extends haxe_unit_TestCase {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function testPrimitives() {
		$data1 = (new _hx_array(array((new _hx_array(array("id", "color", "length"))), (new _hx_array(array(15, "Red", 11.2))))));
		$table1 = harness_Native::table($data1);
		$flags = new coopy_CompareFlags();
		$align = coopy_Coopy::compareTables($table1, $table1, null)->align();
		$diff = harness_Native::table((new _hx_array(array())));
		$highlighter = new coopy_TableDiff($align, $flags);
		$flags->ordered = false;
		$highlighter->hilite($diff);
		$this->assertEquals(1, 1, _hx_anonymous(array("fileName" => "TypeTest.hx", "lineNumber" => 18, "className" => "harness.TypeTest", "methodName" => "testPrimitives")));
	}
	public function testList() {
		$data1 = (new _hx_array(array((new _hx_array(array("id", "color", "length"))), (new _hx_array(array(15, "Red", 11.2))))));
		$table1 = harness_Native::table($data1);
		$flags = new coopy_CompareFlags();
		$align = coopy_Coopy::compareTables($table1, $table1, null)->align();
		$diff = harness_Native::table((new _hx_array(array())));
		$highlighter = new coopy_TableDiff($align, $flags);
		$flags->always_show_order = true;
		$flags->never_show_order = false;
		$highlighter->hilite($diff);
		if($diff->getData() !== null) {
			$this->assertTrue(harness_Native::isList($diff->getData()), _hx_anonymous(array("fileName" => "TypeTest.hx", "lineNumber" => 35, "className" => "harness.TypeTest", "methodName" => "testList")));
			$this->assertTrue(harness_Native::isList(harness_Native::row($diff->getData(), 0)), _hx_anonymous(array("fileName" => "TypeTest.hx", "lineNumber" => 36, "className" => "harness.TypeTest", "methodName" => "testList")));
			$this->assertTrue(harness_Native::isList(harness_Native::row($diff->getData(), 1)), _hx_anonymous(array("fileName" => "TypeTest.hx", "lineNumber" => 37, "className" => "harness.TypeTest", "methodName" => "testList")));
		}
	}
	function __toString() { return 'harness.TypeTest'; }
}
