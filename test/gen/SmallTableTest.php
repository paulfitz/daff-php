<?php

class harness_SmallTableTest extends haxe_unit_TestCase {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public $data1;
	public $data2;
	public function checkDiff($e1, $e2, $verbose = null) {
		if($verbose === null) {
			$verbose = false;
		}
		$table1 = harness_Native::table($e1);
		$table2 = harness_Native::table($e2);
		$data_diff = (new _hx_array(array()));
		$table_diff = harness_Native::table($data_diff);
		$flags = new coopy_CompareFlags();
		$alignment = coopy_Coopy::compareTables($table1, $table2, $flags)->align();
		if($verbose) {
			haxe_Log::trace("Alignment: " . Std::string($alignment), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 18, "className" => "harness.SmallTableTest", "methodName" => "checkDiff")));
		}
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);
		if($verbose) {
			haxe_Log::trace("Diff: " . Std::string($table_diff), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 21, "className" => "harness.SmallTableTest", "methodName" => "checkDiff")));
		}
		$o = coopy_Coopy::diff($table1, $table2, null);
		$this->assertTrue(coopy_SimpleTable::tableIsSimilar($table_diff, $o), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 25, "className" => "harness.SmallTableTest", "methodName" => "checkDiff")));
		$table3 = $table1->hclone();
		$patcher = new coopy_HighlightPatch($table3, $table_diff, null);
		$patcher->apply();
		if($verbose) {
			haxe_Log::trace("Desired " . _hx_string_rec($table2->get_height(), "") . "x" . _hx_string_rec($table2->get_width(), "") . ": " . Std::string($table2), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 30, "className" => "harness.SmallTableTest", "methodName" => "checkDiff")));
		}
		if($verbose) {
			haxe_Log::trace("Got " . _hx_string_rec($table3->get_height(), "") . "x" . _hx_string_rec($table3->get_width(), "") . ": " . Std::string($table3), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 31, "className" => "harness.SmallTableTest", "methodName" => "checkDiff")));
		}
		if($verbose) {
			haxe_Log::trace("Base " . _hx_string_rec($table1->get_height(), "") . "x" . _hx_string_rec($table1->get_width(), "") . ": " . Std::string($table1), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 32, "className" => "harness.SmallTableTest", "methodName" => "checkDiff")));
		}
		$this->assertTrue(coopy_SimpleTable::tableIsSimilar($table3, $table2), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 33, "className" => "harness.SmallTableTest", "methodName" => "checkDiff")));
		return $table_diff;
	}
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
		$this->assertEquals($table_diff->get_height(), 1, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 55, "className" => "harness.SmallTableTest", "methodName" => "testSmall")));
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
		$this->assertEquals($table_diff->get_height(), 1, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 68, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$this->assertEquals($table_diff->get_width(), 3, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 69, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$v = $table1->getCellView();
		$this->assertEquals($v->toString($table_diff->getCell(0, 0)), "@@", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 71, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$this->assertEquals($v->toString($table_diff->getCell(1, 0)), "NAME", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 72, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
		$this->assertEquals($v->toString($table_diff->getCell(2, 0)), "AGE", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 73, "className" => "harness.SmallTableTest", "methodName" => "testIgnore")));
	}
	public function testIssueDaffPhp15() {
		$e1 = (new _hx_array(array((new _hx_array(array("col1", "col2", "col3", "col4", "col5", "col6"))), (new _hx_array(array(0, 0, 0, 0, 2, 0))))));
		$e2 = (new _hx_array(array((new _hx_array(array("col1", "col2", "col3", "col4", "col5", "col6"))), (new _hx_array(array(0, 0, 0, 0, 1, 0))))));
		$table_diff = $this->checkDiff($e1, $e2, null);
		$this->assertEquals($table_diff->get_height(), 2, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 84, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp15")));
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
				$table_diff = $this->checkDiff($e1, $e2, null);
				$this->assertEquals($table_diff->get_height(), 2, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 97, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp16")));
				$this->assertEquals($table_diff->getCell(0, 1), "->", _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 98, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp16")));
				$this->assertEquals($table_diff->getCell(1, 1), "0->" . Std::string($o), _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 99, "className" => "harness.SmallTableTest", "methodName" => "testIssueDaffPhp16")));
				unset($table_diff,$o,$e2,$e1);
			}
		}
	}
	public function testHeaderLikeRow() {
		$e1 = (new _hx_array(array((new _hx_array(array("name1", "name2"))), (new _hx_array(array(0, 0))), (new _hx_array(array("name1", "name2"))))));
		$e2 = (new _hx_array(array((new _hx_array(array("name1", "name2"))), (new _hx_array(array("name1", "name2"))), (new _hx_array(array(0, 0))))));
		$this->checkDiff($e1, $e2, null);
	}
	public function testIssueDaffPhp17() {
		$e1 = (new _hx_array(array((new _hx_array(array("fd", "df"))), (new _hx_array(array("fd", "fd"))), (new _hx_array(array(null, "fd"))), (new _hx_array(array("fd", null))))));
		$e2 = (new _hx_array(array((new _hx_array(array("A", "new_column_2"))), (new _hx_array(array(null, null))), (new _hx_array(array("fd", "df"))), (new _hx_array(array("fd", "fd"))))));
		$this->checkDiff($e1, $e2, null);
	}
	public function testIssueDaffPhp17Edit() {
		$e1 = (new _hx_array(array((new _hx_array(array("fd", "df"))), (new _hx_array(array("fd", "fd"))), (new _hx_array(array(null, "fd"))))));
		$e2 = (new _hx_array(array((new _hx_array(array("A", "new_column_2"))), (new _hx_array(array(null, null))), (new _hx_array(array("fd", "df"))))));
		$this->checkDiff($e1, $e2, null);
	}
	public function testIssueDaffPhp14() {
		$e1 = (new _hx_array(array((new _hx_array(array("A", "new_column_2"))), (new _hx_array(array("dfdf", null))), (new _hx_array(array(null, null))), (new _hx_array(array("xxx", null))), (new _hx_array(array("yyy", null))), (new _hx_array(array(null, null))), (new _hx_array(array("fd", null))), (new _hx_array(array("f", null))), (new _hx_array(array("d", null))), (new _hx_array(array("fdf", null))), (new _hx_array(array(null, null))), (new _hx_array(array(4, null))), (new _hx_array(array(545, null))), (new _hx_array(array(4, null))), (new _hx_array(array(5, null))), (new _hx_array(array(4, null))), (new _hx_array(array(5, null))), (new _hx_array(array(45, null))), (new _hx_array(array(4, null))), (new _hx_array(array(54, null))), (new _hx_array(array(5, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(454, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(4, null))), (new _hx_array(array(5, null))))));
		$e2 = (new _hx_array(array((new _hx_array(array("A", "new_column_2"))), (new _hx_array(array("dfdf", null))), (new _hx_array(array(null, null))), (new _hx_array(array("fd", null))), (new _hx_array(array("fd", null))), (new _hx_array(array(null, null))), (new _hx_array(array("fd", null))), (new _hx_array(array("f", null))), (new _hx_array(array("d", null))), (new _hx_array(array("fdf", null))), (new _hx_array(array(null, null))), (new _hx_array(array(4, null))), (new _hx_array(array(545, null))), (new _hx_array(array(4, null))), (new _hx_array(array(5, null))), (new _hx_array(array(4, null))), (new _hx_array(array(5, null))), (new _hx_array(array(45, null))), (new _hx_array(array(4, null))), (new _hx_array(array(54, null))), (new _hx_array(array(5, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(454, null))), (new _hx_array(array(null, null))), (new _hx_array(array(null, null))), (new _hx_array(array(4, null))), (new _hx_array(array(5, null))))));
		$this->checkDiff($e1, $e2, null);
	}
	public function testStartFromBlank() {
		$e1 = (new _hx_array(array()));
		$e2 = (new _hx_array(array((new _hx_array(array("col1", "col2", "col3"))), (new _hx_array(array(1, 2, 3))))));
		$table_diff = $this->checkDiff($e1, $e2, null);
		$this->assertEquals($table_diff->get_height(), 3, _hx_anonymous(array("fileName" => "SmallTableTest.hx", "lineNumber" => 210, "className" => "harness.SmallTableTest", "methodName" => "testStartFromBlank")));
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
