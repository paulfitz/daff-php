<?php

interface coopy_View {
	function toString($d);
	function equals($d1, $d2);
	function toDatum($str);
	function makeHash();
	function hashSet(&$h, $str, $d);
	function isHash($h);
	function hashExists($h, $str);
	function hashGet($h, $str);
}
