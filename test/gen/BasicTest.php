<?php

class harness_BasicTest extends haxe_unit_TestCase {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public $data1;
	public $data2;
	public $data3;
	public $data4;
	public function setup() {
		$this->data1 = (new _hx_array(array((new _hx_array(array("Country", "Capital"))), (new _hx_array(array("Ireland", "Dublin"))), (new _hx_array(array("France", "Paris"))), (new _hx_array(array("Spain", "Barcelona"))))));
		$this->data2 = (new _hx_array(array((new _hx_array(array("Country", "Code", "Capital"))), (new _hx_array(array("Ireland", "ie", "Dublin"))), (new _hx_array(array("France", "fr", "Paris"))), (new _hx_array(array("Spain", "es", "Madrid"))), (new _hx_array(array("Germany", "de", "Berlin"))))));
		$this->data3 = (new _hx_array(array((new _hx_array(array("Country", "Capital", "Time"))), (new _hx_array(array("Ireland", "Baile Atha Cliath", 0))), (new _hx_array(array("France", "Paris", 1))), (new _hx_array(array("Spain", "Barcelona", 1))))));
		$this->data4 = (new _hx_array(array((new _hx_array(array("Country", "Code", "Capital", "Time"))), (new _hx_array(array("Ireland", "ie", "Baile Atha Cliath", 0))), (new _hx_array(array("France", "fr", "Paris", 1))), (new _hx_array(array("Spain", "es", "Madrid", 1))), (new _hx_array(array("Germany", "de", "Berlin", null))))));
	}
	public function testBasic() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$alignment = coopy_Coopy::compareTables($table1, $table2, null)->align();
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$flags = new coopy_CompareFlags();
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$this->assertEquals("" . Std::string($table_diff->getCell(0, 4)), "->", _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 41, "className" => "harness.BasicTest", "methodName" => "testBasic")));
	}
	public function testNamedID() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$flags = new coopy_CompareFlags();
		$flags->addPrimaryKey("Capital");
		$alignment = coopy_Coopy::compareTables($table1, $table2, $flags)->align();
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$this->assertEquals("" . Std::string($table_diff->getCell(3, 6)), "Barcelona", _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 54, "className" => "harness.BasicTest", "methodName" => "testNamedID")));
	}
	public function testCSV() {
		$txt = "name,age\x0APaul,\"7,9\"\x0A\"Sam\x0ASpace\",\"\"\"\"\x0A";
		$tab = harness_Native::table((new _hx_array(array())));
		$csv = new coopy_Csv(null);
		$csv->parseTable($txt, $tab);
		$this->assertEquals(3, $tab->get_height(), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 62, "className" => "harness.BasicTest", "methodName" => "testCSV")));
		$this->assertEquals(2, $tab->get_width(), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 63, "className" => "harness.BasicTest", "methodName" => "testCSV")));
		$this->assertEquals("Paul", $tab->getCell(0, 1), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 64, "className" => "harness.BasicTest", "methodName" => "testCSV")));
		$this->assertEquals("\"", $tab->getCell(1, 2), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 65, "className" => "harness.BasicTest", "methodName" => "testCSV")));
	}
	public function testEmpty() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table((new _hx_array(array())));
		$alignment = coopy_Coopy::compareTables($table1, $table2, null)->align();
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$flags = new coopy_CompareFlags();
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$table3 = $table1->hclone();
		$patcher = new coopy_HighlightPatch($table3, $table_diff, null);
		$patcher->apply();
		$this->assertEquals(0, $table3->get_height(), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 80, "className" => "harness.BasicTest", "methodName" => "testEmpty")));
	}
	public function testNestedOutput() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$alignment = coopy_Coopy::compareTables($table1, $table2, null)->align();
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$flags = new coopy_CompareFlags();
		$flags->allow_nested_cells = true;
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$update = $table_diff->getCell(3, 4);
		$view = $table_diff->getCellView();
		$this->assertTrue($view->isHash($update), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 96, "className" => "harness.BasicTest", "methodName" => "testNestedOutput")));
		$this->assertEquals("Barcelona", $view->hashGet($update, "before"), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 97, "className" => "harness.BasicTest", "methodName" => "testNestedOutput")));
		$this->assertEquals("Madrid", $view->hashGet($update, "after"), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 98, "className" => "harness.BasicTest", "methodName" => "testNestedOutput")));
		$this->assertEquals("Barcelona", harness_Native::getHashKey($update, "before"), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 99, "className" => "harness.BasicTest", "methodName" => "testNestedOutput")));
		$this->assertEquals("Madrid", harness_Native::getHashKey($update, "after"), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 100, "className" => "harness.BasicTest", "methodName" => "testNestedOutput")));
	}
	public function testNestedOutputHtml() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$alignment = coopy_Coopy::compareTables($table1, $table2, null)->align();
		$table_diff1 = harness_Native::table((new _hx_array(array())));
		$table_diff2 = harness_Native::table((new _hx_array(array())));
		$flags = new coopy_CompareFlags();
		$highlighter1 = new coopy_TableDiff($alignment, $flags);
		$highlighter1->hilite($table_diff1);
		$flags->allow_nested_cells = true;
		$highlighter2 = new coopy_TableDiff($alignment, $flags);
		$highlighter2->hilite($table_diff2);
		$render1 = _hx_deref(new coopy_DiffRender())->render($table_diff1)->html();
		$render2 = _hx_deref(new coopy_DiffRender())->render($table_diff2)->html();
		$this->assertEquals($render1, $render2, _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 117, "className" => "harness.BasicTest", "methodName" => "testNestedOutputHtml")));
	}
	public function testThreeWay() {
		$flags = new coopy_CompareFlags();
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$table3 = harness_Native::table($this->data3);
		$table4 = harness_Native::table($this->data4);
		$flags->parent = $table1;
		$out = coopy_Coopy::diff($table2, $table3, $flags);
		$table2b = $table2->hclone();
		coopy_Coopy::patch($table2b, $out, null);
		$this->assertTrue(coopy_SimpleTable::tableIsSimilar($table4, $table2b), _hx_anonymous(array("fileName" => "BasicTest.hx", "lineNumber" => 130, "className" => "harness.BasicTest", "methodName" => "testThreeWay")));
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
	function __toString() { return 'harness.BasicTest'; }
}
