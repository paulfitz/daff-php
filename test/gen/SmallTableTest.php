<?php

class harness_SmallTableTest extends haxe_unit_TestCase {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public $data1;
	public $data2;
	public function setup() {
		$this->data1 = (new _hx_array(array((new _hx_array(array("NAME", "AGE"))), (new _hx_array(array("Paul", "15"))), (new _hx_array(array("Sam", "89"))))));
		$this->data2 = (new _hx_array(array((new _hx_array(array("key", "version", "NAME", "AGE"))), (new _hx_array(array("ci1f5egka00009xmh16ya9ok5", "1", "Paul", "15"))), (new _hx_array(array("ci1f5egkj00019xmhoiqjd5ui", "1", "Sam", "89"))))));
	}
	public function testSmall() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$alignment = coopy_Coopy::compareTables3($table2, $table1, $table2, null)->align();
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$flags = new coopy_CompareFlags();
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$this->assertEquals($table_diff->get_height(), 1, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 27, "className" => "harness.SmallTableTest", "methodName" => "testSmall")));
	}
	public function testIgnore() {
		$table1 = harness_Native::table($this->data1);
		$table2 = harness_Native::table($this->data2);
		$flags = new coopy_CompareFlags();
		$flags->columns_to_ignore = (new _hx_array(array("key", "version")));
		$alignment = coopy_Coopy::compareTables3($table2, $table1, $table2, $flags)->align();
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$this->assertEquals($table_diff->get_height(), 1, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 40, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$this->assertEquals($table_diff->get_width(), 3, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 41, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$v = $table1->getCellView();
		$this->assertEquals($v->toString($table_diff->getCell(0, 0)), "@@", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 43, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$this->assertEquals($v->toString($table_diff->getCell(1, 0)), "NAME", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 44, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$this->assertEquals($v->toString($table_diff->getCell(2, 0)), "AGE", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 45, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
	}
	public function testIssueDaffPhp15() {
		$e1 = (new _hx_array(array((new _hx_array(array("col1", "col2", "col3", "col4", "col5", "col6"))), (new _hx_array(array(0, 0, 0, 0, 2, 0))))));
		$e2 = (new _hx_array(array((new _hx_array(array("col1", "col2", "col3", "col4", "col5", "col6"))), (new _hx_array(array(0, 0, 0, 0, 1, 0))))));
		$table1 = harness_Native::table($e1);
		$table2 = harness_Native::table($e2);
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$flags = new coopy_CompareFlags();
		$alignment = coopy_Coopy::compareTables($table1, $table2, $flags)->align();
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		$this->assertEquals($table_diff->get_height(), 2, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 63, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp15")));
		$table3 = $table1->hclone();
		$patcher = new coopy_HighlightPatch($table3, $table_diff);
		$patcher->apply();
		$this->assertTrue(coopy_SimpleTable::tableIsSimilar($table3, $table2), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 67, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp15")));
	}
	public function testIssueDaffPhp16() {
		$objs = (new _hx_array(array("xxx", 1)));
		{
			$_g = 0;
			while($_g < $objs->length) {
				$o = $objs[$_g];
				++$_g;
				$e1 = (new _hx_array(array((new _hx_array(array("col1", "col2", "col3", "col4", "col5"))), (new _hx_array(array(0, 0, 0, 0, 0))))));
				$e2 = (new _hx_array(array((new _hx_array(array("col1", "col2", "col3", "col4", "col5"))), (new _hx_array(array($o, 0, 0, 0, 0))))));
				$table1 = harness_Native::table($e1);
				$table2 = harness_Native::table($e2);
				$data_diff = (new _hx_array(array()));
				$table_diff = harness_Native::table($data_diff);
				$flags = new coopy_CompareFlags();
				$alignment = coopy_Coopy::compareTables($table1, $table2, $flags)->align();
				$highlighter = new coopy_TableDiff($alignment, $flags);
				$highlighter->hilite($table_diff);
				$this->assertEquals($table_diff->get_height(), 2, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 87, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp16")));
				$this->assertEquals($table_diff->getCell(0, 1), "->", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 88, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp16")));
				$this->assertEquals($table_diff->getCell(1, 1), "0->" . Std::string($o), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 89, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp16")));
				unset($table_diff,$table2,$table1,$o,$highlighter,$flags,$e2,$e1,$data_diff,$alignment);
			}
		}
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
	function __toString() { return 'harness.SmallTableTest'; }
}
