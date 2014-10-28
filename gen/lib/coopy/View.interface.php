<?php

interface coopy_View {
	function toString($d);
	function equals($d1, $d2);
	function toDatum($str);
}
