<?php

interface coopy_SqlHelper {
	function getTableNames($db);
	function countRows($db, $name);
	function getRowIDs($db, $name);
}
