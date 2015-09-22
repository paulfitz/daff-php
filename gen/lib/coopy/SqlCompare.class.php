<?php

class coopy_SqlCompare {
	public function __construct($db, $local, $remote, $alt, $align = null) {
		if(!php_Boot::$skip_constructor) {
		$this->db = $db;
		$this->local = $local;
		$this->remote = $remote;
		$this->alt = $alt;
		$this->align = $align;
		$this->peered = false;
		if($this->remote->getDatabase()->getNameForAttachment() !== null) {
			if($this->remote->getDatabase()->getNameForAttachment() !== $this->local->getDatabase()->getNameForAttachment()) {
				$local->getDatabase()->getHelper()->attach($db, "__peer__", $this->remote->getDatabase()->getNameForAttachment());
				$this->peered = true;
			}
		}
		$this->alt_peered = false;
		if($this->alt !== null) {
			if($this->alt->getDatabase()->getNameForAttachment() !== null) {
				if($this->alt->getDatabase()->getNameForAttachment() !== $this->local->getDatabase()->getNameForAttachment()) {
					$local->getDatabase()->getHelper()->attach($db, "__alt__", $this->alt->getDatabase()->getNameForAttachment());
					$this->alt_peered = true;
				}
			}
		}
	}}
	public $db;
	public $local;
	public $remote;
	public $alt;
	public $at0;
	public $at1;
	public $at2;
	public $align;
	public $peered;
	public $alt_peered;
	public $needed;
	public function equalArray($a1, $a2) {
		if($a1->length !== $a2->length) {
			return false;
		}
		{
			$_g1 = 0;
			$_g = $a1->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				if($a1[$i] !== $a2[$i]) {
					return false;
				}
				unset($i);
			}
		}
		return true;
	}
	public function validateSchema() {
		$all_cols1 = $this->local->getColumnNames();
		$all_cols2 = $this->remote->getColumnNames();
		$all_cols3 = $all_cols2;
		$key_cols1 = $this->local->getPrimaryKey();
		$key_cols2 = $this->remote->getPrimaryKey();
		$key_cols3 = $key_cols2;
		if($this->alt !== null) {
			$all_cols3 = $this->alt->getColumnNames();
			$key_cols3 = $this->alt->getPrimaryKey();
		}
		if($all_cols1->length === 0 || $all_cols2->length === 0 || $all_cols3->length === 0) {
			throw new HException("Error accessing SQL table");
		}
		if(!($this->equalArray($key_cols1, $key_cols2) && $this->equalArray($key_cols1, $key_cols3))) {
			haxe_Log::trace("sql diff not possible when primary key changes", _hx_anonymous(array("fileName" => "SqlCompare.hx", "lineNumber" => 71, "className" => "coopy.SqlCompare", "methodName" => "validateSchema")));
			return false;
		}
		if($key_cols1->length === 0) {
			haxe_Log::trace("sql diff not possible when primary key not available", _hx_anonymous(array("fileName" => "SqlCompare.hx", "lineNumber" => 75, "className" => "coopy.SqlCompare", "methodName" => "validateSchema")));
			return false;
		}
		return true;
	}
	public function denull($x) {
		if($x === null) {
			return -1;
		}
		return $x;
	}
	public function link() {
		$mode = $this->db->get(0);
		$i0 = $this->denull($this->db->get(1));
		$i1 = $this->denull($this->db->get(2));
		$i2 = $this->denull($this->db->get(3));
		if($i0 === -3) {
			$i0 = $this->at0;
			$this->at0++;
		}
		if($i1 === -3) {
			$i1 = $this->at1;
			$this->at1++;
		}
		if($i2 === -3) {
			$i2 = $this->at2;
			$this->at2++;
		}
		$offset = 4;
		if($i0 >= 0) {
			{
				$_g1 = 0;
				$_g = $this->local->get_width();
				while($_g1 < $_g) {
					$x = $_g1++;
					$this->local->setCellCache($x, $i0, $this->db->get($x + $offset));
					unset($x);
				}
			}
			$offset += $this->local->get_width();
		}
		if($i1 >= 0) {
			{
				$_g11 = 0;
				$_g2 = $this->remote->get_width();
				while($_g11 < $_g2) {
					$x1 = $_g11++;
					$this->remote->setCellCache($x1, $i1, $this->db->get($x1 + $offset));
					unset($x1);
				}
			}
			$offset += $this->remote->get_width();
		}
		if($i2 >= 0) {
			$_g12 = 0;
			$_g3 = $this->alt->get_width();
			while($_g12 < $_g3) {
				$x2 = $_g12++;
				$this->alt->setCellCache($x2, $i2, $this->db->get($x2 + $offset));
				unset($x2);
			}
		}
		if($mode === 0 || $mode === 2) {
			$this->align->link($i0, $i1);
			$this->align->addToOrder($i0, $i1, null);
		}
		if($this->alt !== null) {
			if($mode === 1 || $mode === 2) {
				$this->align->reference->link($i0, $i2);
				$this->align->reference->addToOrder($i0, $i2, null);
			}
		}
	}
	public function linkQuery($query, $order) {
		if($this->db->begin($query, null, $order)) {
			while($this->db->read()) {
				$this->link();
			}
			$this->db->end();
		}
	}
	public function where($txt) {
		if($txt === "") {
			return " WHERE 1 = 0";
		}
		return " WHERE " . _hx_string_or_null($txt);
	}
	public function scanColumns($all_cols1, $all_cols2, $key_cols, $present1, $present2, $align) {
		$align->meta = new coopy_Alignment();
		{
			$_g1 = 0;
			$_g = $all_cols1->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$key = $all_cols1[$i];
				if($present2->exists($key)) {
					$align->meta->link($i, $present2->get($key));
				} else {
					$align->meta->link($i, -1);
				}
				unset($key,$i);
			}
		}
		{
			$_g11 = 0;
			$_g2 = $all_cols2->length;
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$key1 = $all_cols2[$i1];
				if(!$present1->exists($key1)) {
					$align->meta->link(-1, $i1);
				}
				unset($key1,$i1);
			}
		}
		$align->meta->range($all_cols1->length, $all_cols2->length);
		{
			$_g3 = 0;
			while($_g3 < $key_cols->length) {
				$key2 = $key_cols[$_g3];
				++$_g3;
				$unit = new coopy_Unit($present1->get($key2), $present2->get($key2), null);
				$align->addIndexColumns($unit);
				unset($unit,$key2);
			}
		}
	}
	public function apply() {
		if($this->db === null) {
			return null;
		}
		if(!$this->validateSchema()) {
			return null;
		}
		$rowid_name = $this->db->rowid();
		if($this->align === null) {
			$this->align = new coopy_Alignment();
		}
		$key_cols = $this->local->getPrimaryKey();
		$data_cols = $this->local->getAllButPrimaryKey();
		$all_cols = $this->local->getColumnNames();
		$all_cols1 = $this->local->getColumnNames();
		$all_cols2 = $this->remote->getColumnNames();
		$all_cols3 = $all_cols2;
		if($this->alt !== null) {
			$all_cols3 = $this->alt->getColumnNames();
		}
		$data_cols1 = $this->local->getAllButPrimaryKey();
		$data_cols2 = $this->remote->getAllButPrimaryKey();
		$all_common_cols = new _hx_array(array());
		$data_common_cols = new _hx_array(array());
		$present1 = new haxe_ds_StringMap();
		$present2 = new haxe_ds_StringMap();
		$present3 = new haxe_ds_StringMap();
		$present_primary = new haxe_ds_StringMap();
		$has_column_add = false;
		{
			$_g1 = 0;
			$_g = $key_cols->length;
			while($_g1 < $_g) {
				$i = $_g1++;
				$present_primary->set($key_cols[$i], $i);
				unset($i);
			}
		}
		{
			$_g11 = 0;
			$_g2 = $all_cols1->length;
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$key = $all_cols1[$i1];
				$present1->set($key, $i1);
				unset($key,$i1);
			}
		}
		{
			$_g12 = 0;
			$_g3 = $all_cols2->length;
			while($_g12 < $_g3) {
				$i2 = $_g12++;
				$key1 = $all_cols2[$i2];
				if(!$present1->exists($key1)) {
					$has_column_add = true;
				}
				$present2->set($key1, $i2);
				unset($key1,$i2);
			}
		}
		{
			$_g13 = 0;
			$_g4 = $all_cols3->length;
			while($_g13 < $_g4) {
				$i3 = $_g13++;
				$key2 = $all_cols3[$i3];
				if(!$present1->exists($key2)) {
					$has_column_add = true;
				}
				$present3->set($key2, $i3);
				if($present1->exists($key2)) {
					if($present2->exists($key2)) {
						$all_common_cols->push($key2);
						if(!$present_primary->exists($key2)) {
							$data_common_cols->push($key2);
						}
					}
				}
				unset($key2,$i3);
			}
		}
		$this->align->meta = new coopy_Alignment();
		{
			$_g14 = 0;
			$_g5 = $all_cols1->length;
			while($_g14 < $_g5) {
				$i4 = $_g14++;
				$key3 = $all_cols1[$i4];
				if($present2->exists($key3)) {
					$this->align->meta->link($i4, $present2->get($key3));
				} else {
					$this->align->meta->link($i4, -1);
				}
				unset($key3,$i4);
			}
		}
		{
			$_g15 = 0;
			$_g6 = $all_cols2->length;
			while($_g15 < $_g6) {
				$i5 = $_g15++;
				$key4 = $all_cols2[$i5];
				if(!$present1->exists($key4)) {
					$this->align->meta->link(-1, $i5);
				}
				unset($key4,$i5);
			}
		}
		$this->scanColumns($all_cols1, $all_cols2, $key_cols, $present1, $present2, $this->align);
		$this->align->tables($this->local, $this->remote);
		if($this->alt !== null) {
			$this->scanColumns($all_cols1, $all_cols3, $key_cols, $present1, $present3, $this->align->reference);
			$this->align->reference->tables($this->local, $this->alt);
		}
		$sql_table1 = $this->local->getQuotedTableName();
		$sql_table2 = $this->remote->getQuotedTableName();
		$sql_table3 = "";
		if($this->alt !== null) {
			$sql_table3 = $this->alt->getQuotedTableName();
		}
		if($this->peered) {
			$sql_table1 = "main." . _hx_string_or_null($sql_table1);
			$sql_table2 = "__peer__." . _hx_string_or_null($sql_table2);
		}
		if($this->alt_peered) {
			$sql_table2 = "__alt__." . _hx_string_or_null($sql_table3);
		}
		$sql_key_cols = "";
		{
			$_g16 = 0;
			$_g7 = $key_cols->length;
			while($_g16 < $_g7) {
				$i6 = $_g16++;
				if($i6 > 0) {
					$sql_key_cols .= ",";
				}
				$sql_key_cols .= _hx_string_or_null($this->local->getQuotedColumnName($key_cols[$i6]));
				unset($i6);
			}
		}
		$sql_all_cols = "";
		{
			$_g17 = 0;
			$_g8 = $all_common_cols->length;
			while($_g17 < $_g8) {
				$i7 = $_g17++;
				if($i7 > 0) {
					$sql_all_cols .= ",";
				}
				$sql_all_cols .= _hx_string_or_null($this->local->getQuotedColumnName($all_common_cols[$i7]));
				unset($i7);
			}
		}
		$sql_all_cols1 = "";
		{
			$_g18 = 0;
			$_g9 = $all_cols1->length;
			while($_g18 < $_g9) {
				$i8 = $_g18++;
				if($i8 > 0) {
					$sql_all_cols1 .= ",";
				}
				$sql_all_cols1 .= _hx_string_or_null($this->local->getQuotedColumnName($all_cols1[$i8]));
				unset($i8);
			}
		}
		$sql_all_cols2 = "";
		{
			$_g19 = 0;
			$_g10 = $all_cols2->length;
			while($_g19 < $_g10) {
				$i9 = $_g19++;
				if($i9 > 0) {
					$sql_all_cols2 .= ",";
				}
				$sql_all_cols2 .= _hx_string_or_null($this->local->getQuotedColumnName($all_cols2[$i9]));
				unset($i9);
			}
		}
		$sql_all_cols3 = "";
		if($this->alt !== null) {
			$_g110 = 0;
			$_g20 = $all_cols3->length;
			while($_g110 < $_g20) {
				$i10 = $_g110++;
				if($i10 > 0) {
					$sql_all_cols3 .= ",";
				}
				$sql_all_cols3 .= _hx_string_or_null($this->local->getQuotedColumnName($all_cols3[$i10]));
				unset($i10);
			}
		}
		$sql_key_match2 = "";
		{
			$_g111 = 0;
			$_g21 = $key_cols->length;
			while($_g111 < $_g21) {
				$i11 = $_g111++;
				if($i11 > 0) {
					$sql_key_match2 .= " AND ";
				}
				$n = $this->local->getQuotedColumnName($key_cols[$i11]);
				$sql_key_match2 .= _hx_string_or_null($sql_table1) . "." . _hx_string_or_null($n) . " IS " . _hx_string_or_null($sql_table2) . "." . _hx_string_or_null($n);
				unset($n,$i11);
			}
		}
		$sql_key_match3 = "";
		if($this->alt !== null) {
			$_g112 = 0;
			$_g22 = $key_cols->length;
			while($_g112 < $_g22) {
				$i12 = $_g112++;
				if($i12 > 0) {
					$sql_key_match3 .= " AND ";
				}
				$n1 = $this->local->getQuotedColumnName($key_cols[$i12]);
				$sql_key_match3 .= _hx_string_or_null($sql_table1) . "." . _hx_string_or_null($n1) . " IS " . _hx_string_or_null($sql_table3) . "." . _hx_string_or_null($n1);
				unset($n1,$i12);
			}
		}
		$sql_data_mismatch = "";
		{
			$_g113 = 0;
			$_g23 = $data_common_cols->length;
			while($_g113 < $_g23) {
				$i13 = $_g113++;
				if($i13 > 0) {
					$sql_data_mismatch .= " OR ";
				}
				$n2 = $this->local->getQuotedColumnName($data_common_cols[$i13]);
				$sql_data_mismatch .= _hx_string_or_null($sql_table1) . "." . _hx_string_or_null($n2) . " IS NOT " . _hx_string_or_null($sql_table2) . "." . _hx_string_or_null($n2);
				unset($n2,$i13);
			}
		}
		{
			$_g114 = 0;
			$_g24 = $all_cols2->length;
			while($_g114 < $_g24) {
				$i14 = $_g114++;
				$key5 = $all_cols2[$i14];
				if(!$present1->exists($key5)) {
					if($sql_data_mismatch !== "") {
						$sql_data_mismatch .= " OR ";
					}
					$n3 = $this->remote->getQuotedColumnName($key5);
					$sql_data_mismatch .= _hx_string_or_null($sql_table2) . "." . _hx_string_or_null($n3) . " IS NOT NULL";
					unset($n3);
				}
				unset($key5,$i14);
			}
		}
		if($this->alt !== null) {
			{
				$_g115 = 0;
				$_g25 = $data_common_cols->length;
				while($_g115 < $_g25) {
					$i15 = $_g115++;
					if(strlen($sql_data_mismatch) > 0) {
						$sql_data_mismatch .= " OR ";
					}
					$n4 = $this->local->getQuotedColumnName($data_common_cols[$i15]);
					$sql_data_mismatch .= _hx_string_or_null($sql_table1) . "." . _hx_string_or_null($n4) . " IS NOT " . _hx_string_or_null($sql_table3) . "." . _hx_string_or_null($n4);
					unset($n4,$i15);
				}
			}
			{
				$_g116 = 0;
				$_g26 = $all_cols3->length;
				while($_g116 < $_g26) {
					$i16 = $_g116++;
					$key6 = $all_cols3[$i16];
					if(!$present1->exists($key6)) {
						if($sql_data_mismatch !== "") {
							$sql_data_mismatch .= " OR ";
						}
						$n5 = $this->alt->getQuotedColumnName($key6);
						$sql_data_mismatch .= _hx_string_or_null($sql_table3) . "." . _hx_string_or_null($n5) . " IS NOT NULL";
						unset($n5);
					}
					unset($key6,$i16);
				}
			}
		}
		$sql_dbl_cols = "";
		$dbl_cols = (new _hx_array(array()));
		{
			$_g117 = 0;
			$_g27 = $all_cols1->length;
			while($_g117 < $_g27) {
				$i17 = $_g117++;
				if($sql_dbl_cols !== "") {
					$sql_dbl_cols .= ",";
				}
				$buf = "__coopy_" . _hx_string_rec($i17, "");
				$n6 = $this->local->getQuotedColumnName($all_cols1[$i17]);
				$sql_dbl_cols .= _hx_string_or_null($sql_table1) . "." . _hx_string_or_null($n6) . " AS " . _hx_string_or_null($buf);
				$dbl_cols->push($buf);
				unset($n6,$i17,$buf);
			}
		}
		{
			$_g118 = 0;
			$_g28 = $all_cols2->length;
			while($_g118 < $_g28) {
				$i18 = $_g118++;
				if($sql_dbl_cols !== "") {
					$sql_dbl_cols .= ",";
				}
				$buf1 = "__coopy_" . _hx_string_rec($i18, "") . "b";
				$n7 = $this->local->getQuotedColumnName($all_cols2[$i18]);
				$sql_dbl_cols .= _hx_string_or_null($sql_table2) . "." . _hx_string_or_null($n7) . " AS " . _hx_string_or_null($buf1);
				$dbl_cols->push($buf1);
				unset($n7,$i18,$buf1);
			}
		}
		if($this->alt !== null) {
			$_g119 = 0;
			$_g29 = $all_cols3->length;
			while($_g119 < $_g29) {
				$i19 = $_g119++;
				if($sql_dbl_cols !== "") {
					$sql_dbl_cols .= ",";
				}
				$buf2 = "__coopy_" . _hx_string_rec($i19, "") . "c";
				$n8 = $this->local->getQuotedColumnName($all_cols3[$i19]);
				$sql_dbl_cols .= _hx_string_or_null($sql_table3) . "." . _hx_string_or_null($n8) . " AS " . _hx_string_or_null($buf2);
				$dbl_cols->push($buf2);
				unset($n8,$i19,$buf2);
			}
		}
		$sql_order = "";
		{
			$_g120 = 0;
			$_g30 = $key_cols->length;
			while($_g120 < $_g30) {
				$i20 = $_g120++;
				if($i20 > 0) {
					$sql_order .= ",";
				}
				$n9 = $this->local->getQuotedColumnName($key_cols[$i20]);
				$sql_order .= _hx_string_or_null($n9);
				unset($n9,$i20);
			}
		}
		$rowid = "-3";
		$rowid1 = "-3";
		$rowid2 = "-3";
		$rowid3 = "-3";
		if($rowid_name !== null) {
			$rowid = $rowid_name;
			$rowid1 = _hx_string_or_null($sql_table1) . "." . _hx_string_or_null($rowid_name);
			$rowid2 = _hx_string_or_null($sql_table2) . "." . _hx_string_or_null($rowid_name);
			$rowid3 = _hx_string_or_null($sql_table3) . "." . _hx_string_or_null($rowid_name);
		}
		$this->at0 = 1;
		$this->at1 = 1;
		$this->at2 = 1;
		$sql_inserts = "SELECT DISTINCT 0 AS __coopy_code, NULL, " . _hx_string_or_null($rowid) . " AS rowid, NULL, " . _hx_string_or_null($sql_all_cols2) . " FROM " . _hx_string_or_null($sql_table2) . " WHERE NOT EXISTS (SELECT 1 FROM " . _hx_string_or_null($sql_table1) . _hx_string_or_null($this->where($sql_key_match2)) . ")";
		$sql_inserts_order = _hx_deref((new _hx_array(array("__coopy_code", "NULL", "rowid", "NULL"))))->concat($all_cols2);
		$this->linkQuery($sql_inserts, $sql_inserts_order);
		if($this->alt !== null) {
			$sql_inserts1 = "SELECT DISTINCT 1 AS __coopy_code, NULL, NULL, " . _hx_string_or_null($rowid) . " AS rowid, " . _hx_string_or_null($sql_all_cols3) . " FROM " . _hx_string_or_null($sql_table3) . " WHERE NOT EXISTS (SELECT 1 FROM " . _hx_string_or_null($sql_table1) . _hx_string_or_null($this->where($sql_key_match3)) . ")";
			$sql_inserts_order1 = _hx_deref((new _hx_array(array("__coopy_code", "NULL", "NULL", "rowid"))))->concat($all_cols3);
			$this->linkQuery($sql_inserts1, $sql_inserts_order1);
		}
		$sql_updates = "SELECT DISTINCT 2 AS __coopy_code, " . _hx_string_or_null($rowid1) . " AS __coopy_rowid0, " . _hx_string_or_null($rowid2) . " AS __coopy_rowid1, ";
		if($this->alt !== null) {
			$sql_updates .= _hx_string_or_null($rowid3) . " AS __coopy_rowid2,";
		} else {
			$sql_updates .= " NULL,";
		}
		$sql_updates .= _hx_string_or_null($sql_dbl_cols) . " FROM " . _hx_string_or_null($sql_table1);
		if($sql_table1 !== $sql_table2) {
			$sql_updates .= " INNER JOIN " . _hx_string_or_null($sql_table2) . " ON " . _hx_string_or_null($sql_key_match2);
		}
		if($this->alt !== null && $sql_table1 !== $sql_table3) {
			$sql_updates .= " INNER JOIN " . _hx_string_or_null($sql_table3) . " ON " . _hx_string_or_null($sql_key_match3);
		}
		$sql_updates .= _hx_string_or_null($this->where($sql_data_mismatch));
		$sql_updates_order = _hx_deref((new _hx_array(array("__coopy_code", "__coopy_rowid0", "__coopy_rowid1", "__coopy_rowid2"))))->concat($dbl_cols);
		$this->linkQuery($sql_updates, $sql_updates_order);
		if($this->alt === null) {
			$sql_deletes = "SELECT DISTINCT 0 AS __coopy_code, " . _hx_string_or_null($rowid) . " AS rowid, NULL, NULL, " . _hx_string_or_null($sql_all_cols1) . " FROM " . _hx_string_or_null($sql_table1) . " WHERE NOT EXISTS (SELECT 1 FROM " . _hx_string_or_null($sql_table2) . _hx_string_or_null($this->where($sql_key_match2)) . ")";
			$sql_deletes_order = _hx_deref((new _hx_array(array("__coopy_code", "rowid", "NULL", "NULL"))))->concat($all_cols1);
			$this->linkQuery($sql_deletes, $sql_deletes_order);
		}
		if($this->alt !== null) {
			$sql_deletes1 = "SELECT 2 AS __coopy_code, " . _hx_string_or_null($rowid1) . " AS __coopy_rowid0, " . _hx_string_or_null($rowid2) . " AS __coopy_rowid1, ";
			$sql_deletes1 .= _hx_string_or_null($rowid3) . " AS __coopy_rowid2, ";
			$sql_deletes1 .= _hx_string_or_null($sql_dbl_cols);
			$sql_deletes1 .= " FROM " . _hx_string_or_null($sql_table1);
			$sql_deletes1 .= " LEFT OUTER JOIN " . _hx_string_or_null($sql_table2) . " ON " . _hx_string_or_null($sql_key_match2);
			$sql_deletes1 .= " LEFT OUTER JOIN " . _hx_string_or_null($sql_table3) . " ON " . _hx_string_or_null($sql_key_match3);
			$sql_deletes1 .= " WHERE __coopy_rowid1 IS NULL OR __coopy_rowid2 IS NULL";
			$sql_deletes_order1 = _hx_deref((new _hx_array(array("__coopy_code", "__coopy_rowid0", "__coopy_rowid1", "__coopy_rowid2"))))->concat($dbl_cols);
			$this->linkQuery($sql_deletes1, $sql_deletes_order1);
		}
		return $this->align;
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	function __toString() { return 'coopy.SqlCompare'; }
}
