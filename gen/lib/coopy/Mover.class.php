<?php

class coopy_Mover {
	public function __construct(){}
	static function moveUnits($units) {
		$isrc = new _hx_array(array());
		$idest = new _hx_array(array());
		$len = $units->length;
		$ltop = -1;
		$rtop = -1;
		$in_src = new haxe_ds_IntMap();
		$in_dest = new haxe_ds_IntMap();
		{
			$_g = 0;
			while($_g < $len) {
				$i = $_g++;
				$unit = $units[$i];
				if($unit->l >= 0 && $unit->r >= 0) {
					if($ltop < $unit->l) {
						$ltop = $unit->l;
					}
					if($rtop < $unit->r) {
						$rtop = $unit->r;
					}
					{
						$in_src->set($unit->l, $i);
						$i;
					}
					{
						$in_dest->set($unit->r, $i);
						$i;
					}
				}
				unset($unit,$i);
			}
		}
		$v = null;
		{
			$_g1 = 0;
			$_g2 = $ltop + 1;
			while($_g1 < $_g2) {
				$i1 = $_g1++;
				$v = $in_src->get($i1);
				if($v !== null) {
					$isrc->push($v);
				}
				unset($i1);
			}
		}
		{
			$_g11 = 0;
			$_g3 = $rtop + 1;
			while($_g11 < $_g3) {
				$i2 = $_g11++;
				$v = $in_dest->get($i2);
				if($v !== null) {
					$idest->push($v);
				}
				unset($i2);
			}
		}
		return coopy_Mover::moveWithoutExtras($isrc, $idest);
	}
	static function move($isrc, $idest) {
		$len = $isrc->length;
		$len2 = $idest->length;
		$in_src = new haxe_ds_IntMap();
		$in_dest = new haxe_ds_IntMap();
		{
			$_g = 0;
			while($_g < $len) {
				$i = $_g++;
				{
					$in_src->set($isrc[$i], $i);
					$i;
				}
				unset($i);
			}
		}
		{
			$_g1 = 0;
			while($_g1 < $len2) {
				$i1 = $_g1++;
				{
					$in_dest->set($idest[$i1], $i1);
					$i1;
				}
				unset($i1);
			}
		}
		$src = new _hx_array(array());
		$dest = new _hx_array(array());
		$v = null;
		{
			$_g2 = 0;
			while($_g2 < $len) {
				$i2 = $_g2++;
				$v = $isrc[$i2];
				if($in_dest->exists($v)) {
					$src->push($v);
				}
				unset($i2);
			}
		}
		{
			$_g3 = 0;
			while($_g3 < $len2) {
				$i3 = $_g3++;
				$v = $idest[$i3];
				if($in_src->exists($v)) {
					$dest->push($v);
				}
				unset($i3);
			}
		}
		return coopy_Mover::moveWithoutExtras($src, $dest);
	}
	static function moveWithoutExtras($src, $dest) {
		if($src->length !== $dest->length) {
			return null;
		}
		if($src->length <= 1) {
			return (new _hx_array(array()));
		}
		$len = $src->length;
		$in_src = new haxe_ds_IntMap();
		$blk_len = new haxe_ds_IntMap();
		$blk_src_loc = new haxe_ds_IntMap();
		$blk_dest_loc = new haxe_ds_IntMap();
		{
			$_g = 0;
			while($_g < $len) {
				$i = $_g++;
				{
					$in_src->set($src[$i], $i);
					$i;
				}
				unset($i);
			}
		}
		$ct = 0;
		$in_cursor = -2;
		$out_cursor = 0;
		$next = null;
		$blk = -1;
		$v = null;
		while($out_cursor < $len) {
			$v = $dest[$out_cursor];
			$next = $in_src->get($v);
			if($next !== $in_cursor + 1) {
				$blk = $v;
				$ct = 1;
				$blk_src_loc->set($blk, $next);
				$blk_dest_loc->set($blk, $out_cursor);
			} else {
				$ct++;
			}
			$blk_len->set($blk, $ct);
			$in_cursor = $next;
			$out_cursor++;
		}
		$blks = new _hx_array(array());
		if(null == $blk_len) throw new HException('null iterable');
		$__hx__it = $blk_len->keys();
		while($__hx__it->hasNext()) {
			unset($k);
			$k = $__hx__it->next();
			$blks->push($k);
		}
		$blks->sort(array(new _hx_lambda(array(&$blk, &$blk_dest_loc, &$blk_len, &$blk_src_loc, &$blks, &$ct, &$dest, &$in_cursor, &$in_src, &$len, &$next, &$out_cursor, &$src, &$v), "coopy_Mover_0"), 'execute'));
		$moved = new _hx_array(array());
		while($blks->length > 0) {
			$blk1 = $blks->shift();
			$blen = $blks->length;
			$ref_src_loc = $blk_src_loc->get($blk1);
			$ref_dest_loc = $blk_dest_loc->get($blk1);
			$i1 = $blen - 1;
			while($i1 >= 0) {
				$blki = $blks[$i1];
				$blki_src_loc = $blk_src_loc->get($blki);
				$to_left_src = $blki_src_loc < $ref_src_loc;
				$to_left_dest = $blk_dest_loc->get($blki) < $ref_dest_loc;
				if($to_left_src !== $to_left_dest) {
					$ct1 = $blk_len->get($blki);
					{
						$_g1 = 0;
						while($_g1 < $ct1) {
							$j = $_g1++;
							$moved->push($src[$blki_src_loc]);
							$blki_src_loc++;
							unset($j);
						}
						unset($_g1);
					}
					$blks->splice($i1, 1);
					unset($ct1);
				}
				$i1--;
				unset($to_left_src,$to_left_dest,$blki_src_loc,$blki);
			}
			unset($ref_src_loc,$ref_dest_loc,$i1,$blk1,$blen);
		}
		return $moved;
	}
	function __toString() { return 'coopy.Mover'; }
}
function coopy_Mover_0(&$blk, &$blk_dest_loc, &$blk_len, &$blk_src_loc, &$blks, &$ct, &$dest, &$in_cursor, &$in_src, &$len, &$next, &$out_cursor, &$src, &$v, $a, $b) {
	{
		return $blk_len->get($b) - $blk_len->get($a);
	}
}
