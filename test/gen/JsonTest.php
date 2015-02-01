<?php

class harness_JsonTest extends haxe_unit_TestCase {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		parent::__construct();
	}}
	public function testJsonOut() {
		$j = new haxe_ds_StringMap();
		$j->set("a", 1);
		$j->set("b", 2);
		$txt = haxe_format_JsonPrinter::hprint($j, null, null);
		$this->assertTrue($txt === "{\"a\":1,\"b\":2}" || $txt === "{\"b\":2,\"a\":1}", _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 12, "className" => "harness.JsonTest", "methodName" => "testJsonOut")));
	}
	public function testNdjsonOut() {
		$t = harness_Native::table((new _hx_array(array((new _hx_array(array("a", "b"))), (new _hx_array(array(1, 2)))))));
		$txt = _hx_deref(new coopy_Ndjson($t))->renderRow(1);
		$this->assertTrue($txt === "{\"a\":1,\"b\":2}" || $txt === "{\"b\":2,\"a\":1}", _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 18, "className" => "harness.JsonTest", "methodName" => "testNdjsonOut")));
	}
	public function testNdjsonInOneRow() {
		$t = harness_Native::table((new _hx_array(array())));
		_hx_deref(new coopy_Ndjson($t))->parse("{\"a\":1,\"b\":2}");
		$ca = null;
		if(_hx_equal($t->getCell(0, 0), "a")) {
			$ca = 0;
		} else {
			$ca = 1;
		}
		$cb = 1 - $ca;
		$this->assertEquals("a", $t->getCell($ca, 0), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 26, "className" => "harness.JsonTest", "methodName" => "testNdjsonInOneRow")));
		$this->assertEquals("b", $t->getCell($cb, 0), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 27, "className" => "harness.JsonTest", "methodName" => "testNdjsonInOneRow")));
		$this->assertEquals(1, $t->getCell($ca, 1), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 28, "className" => "harness.JsonTest", "methodName" => "testNdjsonInOneRow")));
		$this->assertEquals(2, $t->getCell($cb, 1), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 29, "className" => "harness.JsonTest", "methodName" => "testNdjsonInOneRow")));
	}
	public function testNdjsonIn() {
		$t = harness_Native::table((new _hx_array(array())));
		_hx_deref(new coopy_Ndjson($t))->parse("{\"a\":1,\"b\":2}\x0A{\"a\":11,\"b\":22}\x0D\x0A{\"a\":111,\"b\":222}\x0A");
		$ca = null;
		if(_hx_equal($t->getCell(0, 0), "a")) {
			$ca = 0;
		} else {
			$ca = 1;
		}
		$cb = 1 - $ca;
		$this->assertEquals("a", $t->getCell($ca, 0), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 37, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
		$this->assertEquals("b", $t->getCell($cb, 0), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 38, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
		$this->assertEquals(1, $t->getCell($ca, 1), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 39, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
		$this->assertEquals(2, $t->getCell($cb, 1), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 40, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
		$this->assertEquals(11, $t->getCell($ca, 2), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 41, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
		$this->assertEquals(22, $t->getCell($cb, 2), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 42, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
		$this->assertEquals(111, $t->getCell($ca, 3), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 43, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
		$this->assertEquals(222, $t->getCell($cb, 3), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 44, "className" => "harness.JsonTest", "methodName" => "testNdjsonIn")));
	}
	public function testNdjsonLoop() {
		$t = harness_Native::table((new _hx_array(array())));
		_hx_deref(new coopy_Ndjson($t))->parse("{\"a\":1,\"b\":2}\x0A{\"a\":11,\"b\":22}\x0D\x0A{\"a\":111,\"b\":222}\x0A");
		$txt = _hx_deref(new coopy_Ndjson($t))->render();
		$t2 = harness_Native::table((new _hx_array(array())));
		_hx_deref(new coopy_Ndjson($t2))->parse($txt);
		$ca = null;
		if(_hx_equal($t->getCell(0, 0), "a")) {
			$ca = 0;
		} else {
			$ca = 1;
		}
		$cb = 1 - $ca;
		$this->assertEquals("a", $t->getCell($ca, 0), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 55, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
		$this->assertEquals("b", $t->getCell($cb, 0), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 56, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
		$this->assertEquals(1, $t->getCell($ca, 1), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 57, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
		$this->assertEquals(2, $t->getCell($cb, 1), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 58, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
		$this->assertEquals(11, $t->getCell($ca, 2), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 59, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
		$this->assertEquals(22, $t->getCell($cb, 2), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 60, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
		$this->assertEquals(111, $t->getCell($ca, 3), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 61, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
		$this->assertEquals(222, $t->getCell($cb, 3), _hx_anonymous(array("fileName" => "JsonTest.hx", "lineNumber" => 62, "className" => "harness.JsonTest", "methodName" => "testNdjsonLoop")));
	}
	function __toString() { return 'harness.JsonTest'; }
}
