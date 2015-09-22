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
	public function update($db, $name, $conds, $vals) {
		$q = "UPDATE " . _hx_string_or_null($db->getQuotedTableName($name)) . " SET ";
		$lst = new _hx_array(array());
		if(null == $vals) throw new HException('null iterable');
		$__hx__it = $vals->keys();
		while($__hx__it->hasNext()) {
			unset($k);
			$k = $__hx__it->next();
			if($lst->length > 0) {
				$q .= ", ";
			}
			$q .= _hx_string_or_null($db->getQuotedColumnName($k));
			$q .= " = ?";
			$lst->push($vals->get($k));
		}
		$val_len = $lst->length;
		$q .= " WHERE ";
		if(null == $conds) throw new HException('null iterable');
		$__hx__it = $conds->keys();
		while($__hx__it->hasNext()) {
			unset($k1);
			$k1 = $__hx__it->next();
			if($lst->length > $val_len) {
				$q .= " and ";
			}
			$q .= _hx_string_or_null($db->getQuotedColumnName($k1));
			$q .= " = ?";
			$lst->push($conds->get($k1));
		}
		if(!$db->begin($q, $lst, (new _hx_array(array())))) {
			haxe_Log::trace("Problem with database update", _hx_anonymous(array("fileName" => "SqliteHelper.hx", "lineNumber" => 71, "className" => "coopy.SqliteHelper", "methodName" => "update")));
			return false;
		}
		$db->end();
		return true;
	}
	public function delete($db, $name, $conds) {
		$q = "DELETE FROM " . _hx_string_or_null($db->getQuotedTableName($name)) . " WHERE ";
		$lst = new _hx_array(array());
		if(null == $conds) throw new HException('null iterable');
		$__hx__it = $conds->keys();
		while($__hx__it->hasNext()) {
			unset($k);
			$k = $__hx__it->next();
			if($lst->length > 0) {
				$q .= " and ";
			}
			$q .= _hx_string_or_null($db->getQuotedColumnName($k));
			$q .= " = ?";
			$lst->push($conds->get($k));
		}
		if(!$db->begin($q, $lst, (new _hx_array(array())))) {
			haxe_Log::trace("Problem with database delete", _hx_anonymous(array("fileName" => "SqliteHelper.hx", "lineNumber" => 90, "className" => "coopy.SqliteHelper", "methodName" => "delete")));
			return false;
		}
		$db->end();
		return true;
	}
	public function insert($db, $name, $vals) {
		$q = "INSERT INTO " . _hx_string_or_null($db->getQuotedTableName($name)) . " (";
		$lst = new _hx_array(array());
		if(null == $vals) throw new HException('null iterable');
		$__hx__it = $vals->keys();
		while($__hx__it->hasNext()) {
			unset($k);
			$k = $__hx__it->next();
			if($lst->length > 0) {
				$q .= ",";
			}
			$q .= _hx_string_or_null($db->getQuotedColumnName($k));
			$lst->push($vals->get($k));
		}
		$q .= ") VALUES(";
		$need_comma = false;
		if(null == $vals) throw new HException('null iterable');
		$__hx__it = $vals->keys();
		while($__hx__it->hasNext()) {
			unset($k1);
			$k1 = $__hx__it->next();
			if($need_comma) {
				$q .= ",";
			}
			$q .= "?";
			$need_comma = true;
		}
		$q .= ")";
		if(!$db->begin($q, $lst, (new _hx_array(array())))) {
			haxe_Log::trace("Problem with database insert", _hx_anonymous(array("fileName" => "SqliteHelper.hx", "lineNumber" => 118, "className" => "coopy.SqliteHelper", "methodName" => "insert")));
			return false;
		}
		$db->end();
		return true;
	}
	public function attach($db, $tag, $resource_name) {
		if(!$db->begin("ATTACH ? AS `" . _hx_string_or_null($tag) . "`", (new _hx_array(array($resource_name))), (new _hx_array(array())))) {
			haxe_Log::trace("Failed to attach " . _hx_string_or_null($resource_name) . " as " . _hx_string_or_null($tag), _hx_anonymous(array("fileName" => "SqliteHelper.hx", "lineNumber" => 128, "className" => "coopy.SqliteHelper", "methodName" => "attach")));
			return false;
		}
		$db->end();
		return true;
	}
	public function columnListSql($x) {
		return $x->join(",");
	}
	public function fetchSchema($db, $name) {
		$tname = $db->getQuotedTableName($name);
		$query = "select sql from sqlite_master where name = '" . _hx_string_or_null($tname) . "'";
		if(!$db->begin($query, null, (new _hx_array(array("sql"))))) {
			haxe_Log::trace("Cannot find schema for table " . _hx_string_or_null($tname), _hx_anonymous(array("fileName" => "SqliteHelper.hx", "lineNumber" => 143, "className" => "coopy.SqliteHelper", "methodName" => "fetchSchema")));
			return null;
		}
		$sql = "";
		if($db->read()) {
			$sql = $db->get(0);
		}
		$db->end();
		return $sql;
	}
	public function splitSchema($db, $name, $sql) {
		$preamble = "";
		$parts = new _hx_array(array());
		$double_quote = false;
		$single_quote = false;
		$token = "";
		$nesting = 0;
		{
			$_g1 = 0;
			$_g = strlen($sql);
			while($_g1 < $_g) {
				$i = $_g1++;
				$ch = _hx_char_at($sql, $i);
				if($double_quote || $single_quote) {
					if($double_quote) {
						if($ch === "\"") {
							$double_quote = false;
						}
					}
					if($single_quote) {
						if($ch === "'") {
							$single_quote = false;
						}
					}
					$token .= _hx_string_or_null($ch);
					continue;
				}
				$brk = false;
				if($ch === "(") {
					$nesting++;
					if($nesting === 1) {
						$brk = true;
					}
				} else {
					if($ch === ")") {
						$nesting--;
						if($nesting === 0) {
							$brk = true;
						}
					}
				}
				if($ch === ",") {
					$brk = true;
					if($nesting === 1) {}
				}
				if($brk) {
					if(_hx_char_at($token, 0) === " ") {
						$token = _hx_substr($token, 1, strlen($token));
					}
					if($preamble === "") {
						$preamble = $token;
					} else {
						$parts->push($token);
					}
					$token = "";
				} else {
					$token .= _hx_string_or_null($ch);
				}
				unset($i,$ch,$brk);
			}
		}
		$cols = $db->getColumns($name);
		$name2part = new haxe_ds_StringMap();
		$name2col = new haxe_ds_StringMap();
		{
			$_g11 = 0;
			$_g2 = $cols->length;
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$col = $cols[$i1];
				$name2part->set($col->name, $parts[$i1]);
				$name2col->set($col->name, $cols[$i1]);
				unset($i1,$col);
			}
		}
		return _hx_anonymous(array("preamble" => $preamble, "parts" => $parts, "name2part" => $name2part, "columns" => $cols, "name2column" => $name2col));
	}
	public function exec($db, $query) {
		if(!$db->begin($query, null, null)) {
			haxe_Log::trace("database problem", _hx_anonymous(array("fileName" => "SqliteHelper.hx", "lineNumber" => 224, "className" => "coopy.SqliteHelper", "methodName" => "exec")));
			return false;
		}
		$db->end();
		return true;
	}
	public function alterColumns($db, $name, $columns) {
		$notBlank = array(new _hx_lambda(array(&$columns, &$db, &$name), "coopy_SqliteHelper_0"), 'execute');
		$sql = $this->fetchSchema($db, $name);
		$schema = $this->splitSchema($db, $name, $sql);
		$parts = $schema->parts;
		$nparts = new _hx_array(array());
		$new_column_list = new _hx_array(array());
		$ins_column_list = new _hx_array(array());
		$sel_column_list = new _hx_array(array());
		$meta = $schema->columns;
		{
			$_g1 = 0;
			$_g = $columns->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$c = $columns[$i];
				if($c->name !== null) {
					if($c->prevName !== null) {
						$sel_column_list->push($c->prevName);
						$ins_column_list->push($c->name);
					}
					$orig_type = "";
					$orig_primary = false;
					if($schema->name2column->exists($c->name)) {
						$m = $schema->name2column->get($c->name);
						$orig_type = $m->type_value;
						$orig_primary = $m->primary;
						unset($m);
					}
					$next_type = $orig_type;
					$next_primary = $orig_primary;
					if($c->props !== null) {
						$_g2 = 0;
						$_g3 = $c->props;
						while($_g2 < $_g3->length) {
							$p = $_g3[$_g2];
							++$_g2;
							if($p->name === "type") {
								$next_type = $p->val;
							}
							if($p->name === "pkey") {
								$next_primary = "" . Std::string($p->val) === "1";
							}
							unset($p);
						}
						unset($_g3,$_g2);
					}
					$part = "" . _hx_string_or_null($c->name);
					if(call_user_func_array($notBlank, array($next_type))) {
						$part .= " " . _hx_string_or_null($next_type);
					}
					if($next_primary) {
						$part .= " PRIMARY KEY";
					}
					$nparts->push($part);
					$new_column_list->push($c->name);
					unset($part,$orig_type,$orig_primary,$next_type,$next_primary);
				}
				unset($i,$c);
			}
		}
		if(!$this->exec($db, "BEGIN TRANSACTION")) {
			return false;
		}
		$c1 = $this->columnListSql($ins_column_list);
		$tname = $db->getQuotedTableName($name);
		if(!$this->exec($db, "CREATE TEMPORARY TABLE __coopy_backup(" . _hx_string_or_null($c1) . ")")) {
			return false;
		}
		if(!$this->exec($db, "INSERT INTO __coopy_backup (" . _hx_string_or_null($c1) . ") SELECT " . _hx_string_or_null($c1) . " FROM " . _hx_string_or_null($tname))) {
			return false;
		}
		if(!$this->exec($db, "DROP TABLE " . _hx_string_or_null($tname))) {
			return false;
		}
		if(!$this->exec($db, _hx_string_or_null($schema->preamble) . "(" . _hx_string_or_null($nparts->join(", ")) . ")")) {
			return false;
		}
		if(!$this->exec($db, "INSERT INTO " . _hx_string_or_null($tname) . " (" . _hx_string_or_null($c1) . ") SELECT " . _hx_string_or_null($c1) . " FROM __coopy_backup")) {
			return false;
		}
		if(!$this->exec($db, "DROP TABLE __coopy_backup")) {
			return false;
		}
		if(!$this->exec($db, "COMMIT")) {
			return false;
		}
		return true;
	}
	function __toString() { return 'coopy.SqliteHelper'; }
}
function coopy_SqliteHelper_0(&$columns, &$db, &$name, $x) {
	{
		if($x === null || $x === "" || $x === "null") {
			return false;
		}
		return true;
	}
}
