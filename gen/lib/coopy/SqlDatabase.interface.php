<?php

interface coopy_SqlDatabase {
	function getColumns($name);
	function getQuotedTableName($name);
	function getQuotedColumnName($name);
	function begin($query, $args = null, $order = null);
	function beginRow($name, $row, $order = null);
	function read();
	function get($index);
	function end();
	function width();
	function rowid();
	function getHelper();
	function getNameForAttachment();
}
