<?php

class coopy_HighlightPatch implements coopy_Row{
	public function __construct($source, $patch) {
		if(!php_Boot::$skip_constructor) {
		$this->source = $source;
		$this->patch = $patch;
		$this->view = $patch->getCellView();
		$this->sourceView = $source->getCellView();
	}}
	public $source;
	public $patch;
	public $view;
	public $sourceView;
	public $csv;
	public $header;
	public $headerPre;
	public $headerPost;
	public $headerRename;
	public $headerMove;
	public $modifier;
	public $currentRow;
	public $payloadCol;
	public $payloadTop;
	public $mods;
	public $cmods;
	public $rowInfo;
	public $cellInfo;
	public $rcOffset;
	public $indexes;
	public $sourceInPatchCol;
	public $patchInSourceCol;
	public $patchInSourceRow;
	public $lastSourceRow;
	public $actions;
	public $rowPermutation;
	public $rowPermutationRev;
	public $colPermutation;
	public $colPermutationRev;
	public $haveDroppedColumns;
	public $headerRow;
	public function reset() {
		$this->header = new haxe_ds_IntMap();
		$this->headerPre = new haxe_ds_StringMap();
		$this->headerPost = new haxe_ds_StringMap();
		$this->headerRename = new haxe_ds_StringMap();
		$this->headerMove = null;
		$this->modifier = new haxe_ds_IntMap();
		$this->mods = new _hx_array(array());
		$this->cmods = new _hx_array(array());
		$this->csv = new coopy_Csv(null);
		$this->rcOffset = 0;
		$this->currentRow = -1;
		$this->rowInfo = new coopy_CellInfo();
		$this->cellInfo = new coopy_CellInfo();
		$this->sourceInPatchCol = $this->patchInSourceCol = null;
		$this->patchInSourceRow = new haxe_ds_IntMap();
		$this->indexes = null;
		$this->lastSourceRow = -1;
		$this->actions = new _hx_array(array());
		$this->rowPermutation = null;
		$this->rowPermutationRev = null;
		$this->colPermutation = null;
		$this->colPermutationRev = null;
		$this->haveDroppedColumns = false;
		$this->headerRow = 0;
	}
	public function apply() {
		$this->reset();
		if($this->patch->get_width() < 2) {
			return true;
		}
		if($this->patch->get_height() < 1) {
			return true;
		}
		$this->payloadCol = 1 + $this->rcOffset;
		$this->payloadTop = $this->patch->get_width();
		$corner = $this->patch->getCellView()->toString($this->patch->getCell(0, 0));
		if($corner === "@:@") {
			$this->rcOffset = 1;
		} else {
			$this->rcOffset = 0;
		}
		{
			$_g1 = 0;
			$_g = $this->patch->get_height();
			while($_g1 < $_g) {
				$r = $_g1++;
				$str = $this->view->toString($this->patch->getCell($this->rcOffset, $r));
				$this->actions->push((($str !== null) ? $str : ""));
				unset($str,$r);
			}
		}
		$this->headerRow = $this->rcOffset;
		{
			$_g11 = 0;
			$_g2 = $this->patch->get_height();
			while($_g11 < $_g2) {
				$r1 = $_g11++;
				$this->applyRow($r1);
				unset($r1);
			}
		}
		$this->finishRows();
		$this->finishColumns();
		return true;
	}
	public function needSourceColumns() {
		if($this->sourceInPatchCol !== null) {
			return;
		}
		$this->sourceInPatchCol = new haxe_ds_IntMap();
		$this->patchInSourceCol = new haxe_ds_IntMap();
		$av = $this->source->getCellView();
		{
			$_g1 = 0;
			$_g = $this->source->get_width();
			while($_g1 < $_g) {
				$i = $_g1++;
				$name = $av->toString($this->source->getCell($i, 0));
				$at = $this->headerPre->get($name);
				if($at === null) {
					continue;
				}
				$this->sourceInPatchCol->set($i, $at);
				$this->patchInSourceCol->set($at, $i);
				unset($name,$i,$at);
			}
		}
	}
	public function needSourceIndex() {
		if($this->indexes !== null) {
			return;
		}
		$state = new coopy_TableComparisonState();
		$state->a = $this->source;
		$state->b = $this->source;
		$comp = new coopy_CompareTable($state);
		$comp->storeIndexes();
		$comp->run();
		$comp->align();
		$this->indexes = $comp->getIndexes();
		$this->needSourceColumns();
	}
	public function applyRow($r) {
		$this->currentRow = $r;
		$code = $this->actions[$r];
		if($r === 0 && $this->rcOffset > 0) {} else {
			if($code === "@@") {
				$this->headerRow = $r;
				$this->applyHeader();
				$this->applyAction("@@");
			} else {
				if($code === "!") {
					$this->headerRow = $r;
					$this->applyMeta();
				} else {
					if($code === "+++") {
						$this->applyAction($code);
					} else {
						if($code === "---") {
							$this->applyAction($code);
						} else {
							if($code === "+" || $code === ":") {
								$this->applyAction($code);
							} else {
								if(_hx_index_of($code, "->", null) >= 0) {
									$this->applyAction("->");
								} else {
									$this->lastSourceRow = -1;
								}
							}
						}
					}
				}
			}
		}
	}
	public function getDatum($c) {
		return $this->patch->getCell($c, $this->currentRow);
	}
	public function getString($c) {
		return $this->view->toString($this->getDatum($c));
	}
	public function applyMeta() {
		$_g1 = $this->payloadCol;
		$_g = $this->payloadTop;
		while($_g1 < $_g) {
			$i = $_g1++;
			$name = $this->getString($i);
			if($name === "") {
				continue;
			}
			$this->modifier->set($i, $name);
			unset($name,$i);
		}
	}
	public function applyHeader() {
		{
			$_g1 = $this->payloadCol;
			$_g = $this->payloadTop;
			while($_g1 < $_g) {
				$i = $_g1++;
				$name = $this->getString($i);
				if($name === "...") {
					$this->modifier->set($i, "...");
					$this->haveDroppedColumns = true;
					continue;
				}
				$mod = $this->modifier->get($i);
				$move = false;
				if($mod !== null) {
					if(_hx_char_code_at($mod, 0) === 58) {
						$move = true;
						$mod = _hx_substr($mod, 1, strlen($mod));
					}
				}
				$this->header->set($i, $name);
				if($mod !== null) {
					if(_hx_char_code_at($mod, 0) === 40) {
						$prev_name = _hx_substr($mod, 1, strlen($mod) - 2);
						$this->headerPre->set($prev_name, $i);
						$this->headerPost->set($name, $i);
						$this->headerRename->set($prev_name, $name);
						continue;
						unset($prev_name);
					}
				}
				if($mod !== "+++") {
					$this->headerPre->set($name, $i);
				}
				if($mod !== "---") {
					$this->headerPost->set($name, $i);
				}
				if($move) {
					if($this->headerMove === null) {
						$this->headerMove = new haxe_ds_StringMap();
					}
					$this->headerMove->set($name, 1);
				}
				unset($name,$move,$mod,$i);
			}
		}
		if($this->source->get_height() === 0) {
			$this->applyAction("+++");
		}
	}
	public function lookUp($del = null) {
		if($del === null) {
			$del = 0;
		}
		$at = $this->patchInSourceRow->get($this->currentRow + $del);
		if($at !== null) {
			return $at;
		}
		$result = -1;
		$this->currentRow += $del;
		if($this->currentRow >= 0 && $this->currentRow < $this->patch->get_height()) {
			$_g = 0;
			$_g1 = $this->indexes;
			while($_g < $_g1->length) {
				$idx = $_g1[$_g];
				++$_g;
				$match = $idx->queryByContent($this);
				if($match->spot_a !== 1) {
					continue;
				}
				$result = $match->item_a->lst[0];
				break;
				unset($match,$idx);
			}
		}
		{
			$this->patchInSourceRow->set($this->currentRow, $result);
			$result;
		}
		$this->currentRow -= $del;
		return $result;
	}
	public function applyAction($code) {
		$mod = new coopy_HighlightPatchUnit();
		$mod->code = $code;
		$mod->add = $code === "+++";
		$mod->rem = $code === "---";
		$mod->update = $code === "->";
		$this->needSourceIndex();
		if($this->lastSourceRow === -1) {
			$this->lastSourceRow = $this->lookUp(-1);
		}
		$mod->sourcePrevRow = $this->lastSourceRow;
		$nextAct = $this->actions[$this->currentRow + 1];
		if($nextAct !== "+++" && $nextAct !== "...") {
			$mod->sourceNextRow = $this->lookUp(1);
		}
		if($mod->add) {
			if($this->actions[$this->currentRow - 1] !== "+++") {
				if($this->actions[$this->currentRow - 1] === "@@") {
					$mod->sourcePrevRow = 0;
					$this->lastSourceRow = 0;
				} else {
					$mod->sourcePrevRow = $this->lookUp(-1);
				}
			}
			$mod->sourceRow = $mod->sourcePrevRow;
			if($mod->sourceRow !== -1) {
				$mod->sourceRowOffset = 1;
			}
		} else {
			$mod->sourceRow = $this->lastSourceRow = $this->lookUp(null);
		}
		if($this->actions[$this->currentRow + 1] === "") {
			$this->lastSourceRow = $mod->sourceNextRow;
		}
		$mod->patchRow = $this->currentRow;
		if($code === "@@") {
			$mod->sourceRow = 0;
		}
		$this->mods->push($mod);
	}
	public function checkAct() {
		$act = $this->getString($this->rcOffset);
		if($this->rowInfo->value !== $act) {
			coopy_DiffRender::examineCell(0, 0, $this->view, $act, "", $act, "", $this->rowInfo, null);
		}
	}
	public function getPreString($txt) {
		$this->checkAct();
		if(!$this->rowInfo->updated) {
			return $txt;
		}
		coopy_DiffRender::examineCell(0, 0, $this->view, $txt, "", $this->rowInfo->value, "", $this->cellInfo, null);
		if(!$this->cellInfo->updated) {
			return $txt;
		}
		return $this->cellInfo->lvalue;
	}
	public function getRowString($c) {
		$at = $this->sourceInPatchCol->get($c);
		if($at === null) {
			return "NOT_FOUND";
		}
		return $this->getPreString($this->getString($at));
	}
	public function isPreamble() {
		return $this->currentRow <= $this->headerRow;
	}
	public function sortMods($a, $b) {
		if($b->code === "@@" && $a->code !== "@@") {
			return 1;
		}
		if($a->code === "@@" && $b->code !== "@@") {
			return -1;
		}
		if($a->sourceRow === -1 && !$a->add && $b->sourceRow !== -1) {
			return 1;
		}
		if($a->sourceRow !== -1 && !$b->add && $b->sourceRow === -1) {
			return -1;
		}
		if($a->sourceRow + $a->sourceRowOffset > $b->sourceRow + $b->sourceRowOffset) {
			return 1;
		}
		if($a->sourceRow + $a->sourceRowOffset < $b->sourceRow + $b->sourceRowOffset) {
			return -1;
		}
		if($a->patchRow > $b->patchRow) {
			return 1;
		}
		if($a->patchRow < $b->patchRow) {
			return -1;
		}
		return 0;
	}
	public function processMods($rmods, $fate, $len) {
		$rmods->sort((isset($this->sortMods) ? $this->sortMods: array($this, "sortMods")));
		$offset = 0;
		$last = -1;
		$target = 0;
		if($rmods->length > 0) {
			if(_hx_array_get($rmods, 0)->sourcePrevRow === -1) {
				$last = 0;
			}
		}
		{
			$_g = 0;
			while($_g < $rmods->length) {
				$mod = $rmods[$_g];
				++$_g;
				if($last !== -1) {
					$_g2 = $last;
					$_g1 = $mod->sourceRow + $mod->sourceRowOffset;
					while($_g2 < $_g1) {
						$i = $_g2++;
						$fate->push($i + $offset);
						$target++;
						$last++;
						unset($i);
					}
					unset($_g2,$_g1);
				}
				if($mod->rem) {
					$fate->push(-1);
					$offset--;
				} else {
					if($mod->add) {
						$mod->destRow = $target;
						$target++;
						$offset++;
					} else {
						$mod->destRow = $target;
					}
				}
				if($mod->sourceRow >= 0) {
					$last = $mod->sourceRow + $mod->sourceRowOffset;
					if($mod->rem) {
						$last++;
					}
				} else {
					if($mod->add && $mod->sourceNextRow !== -1) {
						$last = $mod->sourceNextRow + $mod->sourceRowOffset;
					} else {
						$last = -1;
					}
				}
				unset($mod);
			}
		}
		if($last !== -1) {
			$_g3 = $last;
			while($_g3 < $len) {
				$i1 = $_g3++;
				$fate->push($i1 + $offset);
				$target++;
				$last++;
				unset($i1);
			}
		}
		return $len + $offset;
	}
	public function computeOrdering($mods, $permutation, $permutationRev, $dim) {
		$to_unit = new haxe_ds_IntMap();
		$from_unit = new haxe_ds_IntMap();
		$meta_from_unit = new haxe_ds_IntMap();
		$ct = 0;
		{
			$_g = 0;
			while($_g < $mods->length) {
				$mod = $mods[$_g];
				++$_g;
				if($mod->add || $mod->rem) {
					continue;
				}
				if($mod->sourceRow < 0) {
					continue;
				}
				if($mod->sourcePrevRow >= 0) {
					{
						$v = $mod->sourceRow;
						$to_unit->set($mod->sourcePrevRow, $v);
						$v;
						unset($v);
					}
					{
						$v1 = $mod->sourcePrevRow;
						$from_unit->set($mod->sourceRow, $v1);
						$v1;
						unset($v1);
					}
					if($mod->sourcePrevRow + 1 !== $mod->sourceRow) {
						$ct++;
					}
				}
				if($mod->sourceNextRow >= 0) {
					{
						$v2 = $mod->sourceNextRow;
						$to_unit->set($mod->sourceRow, $v2);
						$v2;
						unset($v2);
					}
					{
						$v3 = $mod->sourceRow;
						$from_unit->set($mod->sourceNextRow, $v3);
						$v3;
						unset($v3);
					}
					if($mod->sourceRow + 1 !== $mod->sourceNextRow) {
						$ct++;
					}
				}
				unset($mod);
			}
		}
		if($ct > 0) {
			$cursor = null;
			$logical = null;
			$starts = (new _hx_array(array()));
			{
				$_g1 = 0;
				while($_g1 < $dim) {
					$i = $_g1++;
					$u = $from_unit->get($i);
					if($u !== null) {
						{
							$meta_from_unit->set($u, $i);
							$i;
						}
					} else {
						$starts->push($i);
					}
					unset($u,$i);
				}
			}
			$used = new haxe_ds_IntMap();
			$len = 0;
			{
				$_g2 = 0;
				while($_g2 < $dim) {
					$i1 = $_g2++;
					if($logical !== null && $meta_from_unit->exists($logical)) {
						$cursor = $meta_from_unit->get($logical);
					} else {
						$cursor = null;
					}
					if($cursor === null) {
						$v4 = $starts->shift();
						$cursor = $v4;
						$logical = $v4;
						unset($v4);
					}
					if($cursor === null) {
						$cursor = 0;
					}
					while($used->exists($cursor)) {
						$cursor = _hx_mod(($cursor + 1), $dim);
					}
					$logical = $cursor;
					$permutationRev->push($cursor);
					{
						$used->set($cursor, 1);
						1;
					}
					unset($i1);
				}
			}
			{
				$_g11 = 0;
				$_g3 = $permutationRev->length;
				while($_g11 < $_g3) {
					$i2 = $_g11++;
					$permutation[$i2] = -1;
					unset($i2);
				}
			}
			{
				$_g12 = 0;
				$_g4 = $permutation->length;
				while($_g12 < $_g4) {
					$i3 = $_g12++;
					$permutation[$permutationRev[$i3]] = $i3;
					unset($i3);
				}
			}
		}
	}
	public function permuteRows() {
		$this->rowPermutation = new _hx_array(array());
		$this->rowPermutationRev = new _hx_array(array());
		$this->computeOrdering($this->mods, $this->rowPermutation, $this->rowPermutationRev, $this->source->get_height());
	}
	public function finishRows() {
		$fate = new _hx_array(array());
		$this->permuteRows();
		if($this->rowPermutation->length > 0) {
			$_g = 0;
			$_g1 = $this->mods;
			while($_g < $_g1->length) {
				$mod = $_g1[$_g];
				++$_g;
				if($mod->sourceRow >= 0) {
					$mod->sourceRow = $this->rowPermutation[$mod->sourceRow];
				}
				unset($mod);
			}
		}
		if($this->rowPermutation->length > 0) {
			$this->source->insertOrDeleteRows($this->rowPermutation, $this->rowPermutation->length);
		}
		$len = $this->processMods($this->mods, $fate, $this->source->get_height());
		$this->source->insertOrDeleteRows($fate, $len);
		{
			$_g2 = 0;
			$_g11 = $this->mods;
			while($_g2 < $_g11->length) {
				$mod1 = $_g11[$_g2];
				++$_g2;
				if(!$mod1->rem) {
					if($mod1->add) {
						if(null == $this->headerPost) throw new HException('null iterable');
						$__hx__it = $this->headerPost->iterator();
						while($__hx__it->hasNext()) {
							unset($c);
							$c = $__hx__it->next();
							$offset = $this->patchInSourceCol->get($c);
							if($offset !== null && $offset >= 0) {
								$this->source->setCell($offset, $mod1->destRow, $this->patch->getCell($c, $mod1->patchRow));
							}
							unset($offset);
						}
					} else {
						if($mod1->update) {
							$this->currentRow = $mod1->patchRow;
							$this->checkAct();
							if(!$this->rowInfo->updated) {
								continue;
							}
							if(null == $this->headerPre) throw new HException('null iterable');
							$__hx__it = $this->headerPre->iterator();
							while($__hx__it->hasNext()) {
								unset($c1);
								$c1 = $__hx__it->next();
								$txt = $this->view->toString($this->patch->getCell($c1, $mod1->patchRow));
								coopy_DiffRender::examineCell(0, 0, $this->view, $txt, "", $this->rowInfo->value, "", $this->cellInfo, null);
								if(!$this->cellInfo->updated) {
									continue;
								}
								if($this->cellInfo->conflicted) {
									continue;
								}
								$d = $this->view->toDatum($this->csv->parseCell($this->cellInfo->rvalue));
								$this->source->setCell($this->patchInSourceCol->get($c1), $mod1->destRow, $d);
								unset($txt,$d);
							}
						}
					}
				}
				unset($mod1);
			}
		}
	}
	public function permuteColumns() {
		if($this->headerMove === null) {
			return;
		}
		$this->colPermutation = new _hx_array(array());
		$this->colPermutationRev = new _hx_array(array());
		$this->computeOrdering($this->cmods, $this->colPermutation, $this->colPermutationRev, $this->source->get_width());
		if($this->colPermutation->length === 0) {
			return;
		}
	}
	public function finishColumns() {
		$this->needSourceColumns();
		{
			$_g1 = $this->payloadCol;
			$_g = $this->payloadTop;
			while($_g1 < $_g) {
				$i = $_g1++;
				$act = $this->modifier->get($i);
				$hdr = $this->header->get($i);
				if($act === null) {
					$act = "";
				}
				if($act === "---") {
					$at = $this->patchInSourceCol->get($i);
					$mod = new coopy_HighlightPatchUnit();
					$mod->code = $act;
					$mod->rem = true;
					$mod->sourceRow = $at;
					$mod->patchRow = $i;
					$this->cmods->push($mod);
					unset($mod,$at);
				} else {
					if($act === "+++") {
						$mod1 = new coopy_HighlightPatchUnit();
						$mod1->code = $act;
						$mod1->add = true;
						$prev = -1;
						$cont = false;
						$mod1->sourceRow = -1;
						if($this->cmods->length > 0) {
							$mod1->sourceRow = _hx_array_get($this->cmods, $this->cmods->length - 1)->sourceRow;
						}
						if($mod1->sourceRow !== -1) {
							$mod1->sourceRowOffset = 1;
						}
						$mod1->patchRow = $i;
						$this->cmods->push($mod1);
						unset($prev,$mod1,$cont);
					} else {
						if($act !== "...") {
							$mod2 = new coopy_HighlightPatchUnit();
							$mod2->code = $act;
							$mod2->patchRow = $i;
							$mod2->sourceRow = $this->patchInSourceCol->get($i);
							$this->cmods->push($mod2);
							unset($mod2);
						}
					}
				}
				unset($i,$hdr,$act);
			}
		}
		$at1 = -1;
		$rat = -1;
		{
			$_g11 = 0;
			$_g2 = $this->cmods->length - 1;
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$icode = _hx_array_get($this->cmods, $i1)->code;
				if($icode !== "+++" && $icode !== "---") {
					$at1 = _hx_array_get($this->cmods, $i1)->sourceRow;
				}
				_hx_array_get($this->cmods, $i1 + 1)->sourcePrevRow = $at1;
				$j = $this->cmods->length - 1 - $i1;
				$jcode = _hx_array_get($this->cmods, $j)->code;
				if($jcode !== "+++" && $jcode !== "---") {
					$rat = _hx_array_get($this->cmods, $j)->sourceRow;
				}
				_hx_array_get($this->cmods, $j - 1)->sourceNextRow = $rat;
				unset($jcode,$j,$icode,$i1);
			}
		}
		$fate = new _hx_array(array());
		$this->permuteColumns();
		if($this->headerMove !== null) {
			if($this->colPermutation->length > 0) {
				{
					$_g3 = 0;
					$_g12 = $this->cmods;
					while($_g3 < $_g12->length) {
						$mod3 = $_g12[$_g3];
						++$_g3;
						if($mod3->sourceRow >= 0) {
							$mod3->sourceRow = $this->colPermutation[$mod3->sourceRow];
						}
						unset($mod3);
					}
				}
				$this->source->insertOrDeleteColumns($this->colPermutation, $this->colPermutation->length);
			}
		}
		$len = $this->processMods($this->cmods, $fate, $this->source->get_width());
		$this->source->insertOrDeleteColumns($fate, $len);
		{
			$_g4 = 0;
			$_g13 = $this->cmods;
			while($_g4 < $_g13->length) {
				$cmod = $_g13[$_g4];
				++$_g4;
				if(!$cmod->rem) {
					if($cmod->add) {
						{
							$_g21 = 0;
							$_g31 = $this->mods;
							while($_g21 < $_g31->length) {
								$mod4 = $_g31[$_g21];
								++$_g21;
								if($mod4->patchRow !== -1 && $mod4->destRow !== -1) {
									$d = $this->patch->getCell($cmod->patchRow, $mod4->patchRow);
									$this->source->setCell($cmod->destRow, $mod4->destRow, $d);
									unset($d);
								}
								unset($mod4);
							}
							unset($_g31,$_g21);
						}
						$hdr1 = $this->header->get($cmod->patchRow);
						$this->source->setCell($cmod->destRow, 0, $this->view->toDatum($hdr1));
						unset($hdr1);
					}
				}
				unset($cmod);
			}
		}
		{
			$_g14 = 0;
			$_g5 = $this->source->get_width();
			while($_g14 < $_g5) {
				$i2 = $_g14++;
				$name = $this->view->toString($this->source->getCell($i2, 0));
				$next_name = $this->headerRename->get($name);
				if($next_name === null) {
					continue;
				}
				$this->source->setCell($i2, 0, $this->view->toDatum($next_name));
				unset($next_name,$name,$i2);
			}
		}
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
	function __toString() { return 'coopy.HighlightPatch'; }
}
