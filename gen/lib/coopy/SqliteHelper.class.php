<?php

class coopy_SqliteHelper implements coopy_SqlHelper{
	public function __construct() {}
	public function getTableNames($db) { if(!php_Boot::$skip_constructor) {
		$q = "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name";
		if(!$db->begin($q, null, (new _hx_array(array("name"))))) {
			return null;
		}
		$names = new _hx_array(array());
		while($db->read()) {
			$names->push($db->get(0));
		}
		$db->end();
		return $names;
	}}
	public function countRows($db, $name) {
		$q = "SELECT COUNT(*) AS ct FROM " . _hx_string_or_null($db->getQuotedTableName($name));
		if(!$db->begin($q, null, (new _hx_array(array("ct"))))) {
			return -1;
		}
		$ct = -1;
		while($db->read()) {
			$ct = $db->get(0);
		}
		$db->end();
		return $ct;
	}
	public function getRowIDs($db, $name) {
		$result = new _hx_array(array());
		$q = "SELECT ROWID AS r FROM " . _hx_string_or_null($db->getQuotedTableName($name)) . " ORDER BY ROWID";
		if(!$db->begin($q, null, (new _hx_array(array("r"))))) {
			return null;
		}
		while($db->read()) {
			$c = $db->get(0);
			$result->push($c);
			unset($c);
		}
		$db->end();
		return $result;
	}
	function __toString() { return 'coopy.SqliteHelper'; }
}
