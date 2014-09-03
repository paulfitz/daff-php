<?php

interface coopy_View {
	function toString($d);
	function getBag($d);
	function getTable($d);
	function hasStructure($d);
	function equals($d1, $d2);
	function toDatum($str);
}
