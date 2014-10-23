<?php

class harness_RowOrderTest extends haxe_unit_TestCase {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function testRowOrder() {
		$data1 = (new _hx_array(array((new _hx_array(array("Country", "Capital"))), (new _hx_array(array("Ireland", "Dublin"))), (new _hx_array(array("France", "Paris"))), (new _hx_array(array("Spain", "Barcelona"))))));
		$data2 = (new _hx_array(array((new _hx_array(array("Country", "Code", "Capital"))), (new _hx_array(array("Ireland", "ie", "Dublin"))), (new _hx_array(array("France", "fr", "Paris"))), (new _hx_array(array("Spain", "es", "Madrid"))), (new _hx_array(array("Germany", "de", "Berlin"))))));
		$table1 = harness_Native::table($data1);
		$table2 = harness_Native::table($data2);
		$alignment = coopy_Coopy::compareTables($table1, $table2, null)->align();
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$flags = new coopy_CompareFlags();
		$flags->always_show_order = true;
		$flags->never_show_order = false;
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$txt = "" . Std::string($table_diff);
		$this->assertEquals("-:4", $table_diff->getCell(0, 6), _hx_anonymous(array("fileName" => "RowOrderTest.hx", "lineNumber" => 27, "className" => "harness.RowOrderTest", "methodName" => "testRowOrder")));
	}
	function __toString() { return 'harness.RowOrderTest'; }
}
