<?php

interface coopy_Meta {
	function alterColumns($columns);
	function changeRow($rc);
	function applyFlags($flags);
	function asTable();
	function cloneMeta($table = null);
	function useForColumnChanges();
	function useForRowChanges();
	function getRowStream();
	function isNested();
	function isSql();
	function getName();
}
