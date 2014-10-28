<?php

interface coopy_Table {
	function getCell($x, $y);
	function setCell($x, $y, $c);
	function getCellView();
	function isResizable();
	function resize($w, $h);
	function clear();
	function insertOrDeleteRows($fate, $hfate);
	function insertOrDeleteColumns($fate, $wfate);
	function trimBlank();
	function get_width();
	function get_height();
	function getData();
	function hclone();
}
