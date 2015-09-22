<?php

class coopy_HighlightPatch implements coopy_Row{
	public function __construct($source, $patch, $flags = null) {
		if(!php_Boot::$skip_constructor) {
		$this->source = $source;
		$this->patch = $patch;
		$this->flags = $flags;
		if($flags === null) {
			$this->flags = new coopy_CompareFlags();
		}
		$this->view = $patch->getCellView();
		$this->sourceView = $source->getCellView();
		$this->meta = $source->getMeta();
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
	public $destInPatchCol;
	public $patchInDestCol;
	public $patchInSourceRow;
	public $lastSourceRow;
	public $actions;
	public $rowPermutation;
	public $rowPermutationRev;
	public $colPermutation;
	public $colPermutationRev;
	public $haveDroppedColumns;
	public $headerRow;
	public $preambleRow;
	public $flags;
	public $meta_change;
	public $process_meta;
	public $prev_meta;
	public $next_meta;
	public $finished_columns;
	public $meta;
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
		$this->sourceInPatchCol = $this->patchInSourceCol = $this->patchInDestCol = null;
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
		$this->preambleRow = 0;
		$this->meta_change = false;
		$this->process_meta = false;
		$this->prev_meta = null;
		$this->next_meta = null;
		$this->finished_columns = false;
	}
	public function processMeta() {
		$this->process_meta = true;
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
		$this->preambleRow = $this->headerRow = $this->rcOffset;
		{
			$_g11 = 0;
			$_g2 = $this->patch->get_height();
			while($_g11 < $_g2) {
				$r1 = $_g11++;
				$this->applyRow($r1);
				unset($r1);
			}
		}
		$this->finishColumns();
		$this->finishRows();
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
	public function needDestColumns() {
		if($this->patchInDestCol !== null) {
			return;
		}
		$this->patchInDestCol = new haxe_ds_IntMap();
		$this->destInPatchCol = new haxe_ds_IntMap();
		{
			$_g = 0;
			$_g1 = $this->cmods;
			while($_g < $_g1->length) {
				$cmod = $_g1[$_g];
				++$_g;
				if($cmod->patchRow !== -1) {
					$this->patchInDestCol->set($cmod->patchRow, $cmod->destRow);
					$this->destInPatchCol->set($cmod->destRow, $cmod->patchRow);
				}
				unset($cmod);
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
	public function setMetaProp($target, $column_name, $prop_name, $value) {
		if($column_name === null) {
			return;
		}
		if($prop_name === null) {
			return;
		}
		if(!$target->exists($column_name)) {
			$value1 = new _hx_array(array());
			$target->set($column_name, $value1);
		}
		$change = new coopy_PropertyChange();
		$change->prevName = $prop_name;
		$change->name = $prop_name;
		if(_hx_equal($value, "")) {
			$value = null;
		}
		$change->val = $value;
		$target->get($column_name)->push($change);
	}
	public function applyMetaRow($code) {
		$this->needSourceColumns();
		$codes = _hx_explode("@", $code);
		$prop_name = "";
		if($codes->length > 1) {
			$prop_name = $codes[$codes->length - 2];
		}
		if($codes->length > 0) {
			$code = $codes[$codes->length - 1];
		}
		if($this->prev_meta === null) {
			$this->prev_meta = new haxe_ds_StringMap();
		}
		if($this->next_meta === null) {
			$this->next_meta = new haxe_ds_StringMap();
		}
		{
			$_g1 = $this->payloadCol;
			$_g = $this->payloadTop;
			while($_g1 < $_g) {
				$i = $_g1++;
				$txt = $this->getString($i);
				$idx_patch = $i;
				$idx_src = null;
				if($this->patchInSourceCol->exists($idx_patch)) {
					$idx_src = $this->patchInSourceCol->get($idx_patch);
				} else {
					$idx_src = -1;
				}
				$prev_name = null;
				$name = null;
				if($idx_src !== -1) {
					$prev_name = $this->source->getCell($idx_src, 0);
				}
				if($this->header->exists($idx_patch)) {
					$name = $this->header->get($idx_patch);
				}
				coopy_DiffRender::examineCell(0, 0, $this->view, $txt, "", $code, "", $this->cellInfo, null);
				if($this->cellInfo->updated) {
					$this->setMetaProp($this->prev_meta, $prev_name, $prop_name, $this->cellInfo->lvalue);
					$this->setMetaProp($this->next_meta, $name, $prop_name, $this->cellInfo->rvalue);
				} else {
					$this->setMetaProp($this->prev_meta, $prev_name, $prop_name, $this->cellInfo->value);
					$this->setMetaProp($this->next_meta, $name, $prop_name, $this->cellInfo->value);
				}
				unset($txt,$prev_name,$name,$idx_src,$idx_patch,$i);
			}
		}
	}
	public function applyRow($r) {
		$this->currentRow = $r;
		$code = $this->actions[$r];
		$done = false;
		if($r === 0 && $this->rcOffset > 0) {
			$done = true;
		} else {
			if($code === "@@") {
				$this->preambleRow = $this->headerRow = $r;
				$this->applyHeader();
				$this->applyAction("@@");
				$done = true;
			} else {
				if($code === "!") {
					$this->preambleRow = $this->headerRow = $r;
					$this->applyMeta();
					$done = true;
				} else {
					if(_hx_index_of($code, "@", null) === 0) {
						$this->flags->addWarning("cannot usefully apply diffs with metadata yet: '" . _hx_string_or_null($code) . "'");
						$this->preambleRow = $r;
						$this->applyMetaRow($code);
						if($this->process_meta) {
							$codes = _hx_explode("@", $code);
							if($codes->length > 0) {
								$code = $codes[$codes->length - 1];
							}
						} else {
							$this->meta_change = true;
							$done = true;
						}
						$this->meta_change = true;
						$done = true;
					}
				}
			}
		}
		if($this->process_meta) {
			return;
		}
		if(!$done) {
			$this->finishColumns();
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
		if(!$this->useMetaForRowChanges()) {
			if($this->source->get_height() === 0) {
				$this->applyAction("+++");
			}
		}
	}
	public function lookUp($del = null) {
		if($del === null) {
			$del = 0;
		}
		if($this->patchInSourceRow->exists($this->currentRow + $del)) {
			return $this->patchInSourceRow->get($this->currentRow + $del);
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
				if($match->spot_a === 0) {
					continue;
				}
				if($match->spot_a === 1) {
					$result = $match->item_a->lst[0];
					break;
				}
				if($this->currentRow > 0) {
					$prev = $this->patchInSourceRow->get($this->currentRow - 1);
					if($prev !== null) {
						$lst = $match->item_a->lst;
						{
							$_g2 = 0;
							while($_g2 < $lst->length) {
								$row = $lst[$_g2];
								++$_g2;
								if($row === $prev + 1) {
									$result = $row;
									break;
								}
								unset($row);
							}
							unset($_g2);
						}
						unset($lst);
					}
					unset($prev);
				}
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
	public function applyActionExternal($code) {
		if($code === "@@") {
			return;
		}
		$rc = new coopy_RowChange();
		$rc->action = $code;
		$this->checkAct();
		if($code !== "+++") {
			$rc->cond = new haxe_ds_StringMap();
		}
		if($code !== "---") {
			$rc->val = new haxe_ds_StringMap();
		}
		$have_column = false;
		{
			$_g1 = $this->payloadCol;
			$_g = $this->payloadTop;
			while($_g1 < $_g) {
				$i = $_g1++;
				$prev_name = $this->header->get($i);
				$name = $prev_name;
				if($this->headerRename->exists($prev_name)) {
					$name = $this->headerRename->get($prev_name);
				}
				$cact = $this->modifier->get($i);
				if($cact === "...") {
					continue;
				}
				if($name === null || $name === "") {
					continue;
				}
				$txt = $this->getString($i);
				$updated = false;
				if($this->rowInfo->updated) {
					$this->getPreString($txt);
					$updated = $this->cellInfo->updated;
				}
				if($cact === "+++" && $code !== "---") {
					if($txt !== null && $txt !== "") {
						if($rc->val === null) {
							$rc->val = new haxe_ds_StringMap();
						}
						$rc->val->set($name, $txt);
						$have_column = true;
					}
				}
				if($updated) {
					$rc->cond->set($name, $this->cellInfo->lvalue);
					$rc->val->set($name, $this->cellInfo->rvalue);
				} else {
					if($code === "+++") {
						if($cact !== "---") {
							$rc->val->set($name, $txt);
						}
					} else {
						if($cact !== "+++" && $cact !== "---") {
							$rc->cond->set($name, $txt);
						}
					}
				}
				unset($updated,$txt,$prev_name,$name,$i,$cact);
			}
		}
		if($rc->action === "+") {
			if(!$have_column) {
				return;
			}
			$rc->action = "->";
		}
		$this->meta->changeRow($rc);
	}
	public function applyAction($code) {
		if($this->useMetaForRowChanges()) {
			$this->applyActionExternal($code);
			return;
		}
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
		return $this->currentRow <= $this->preambleRow;
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
						if($mod->rem || $mod->add) {
							$last = -1;
						}
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
	public function useMetaForColumnChanges() {
		if($this->meta === null) {
			return false;
		}
		return $this->meta->useForColumnChanges();
	}
	public function useMetaForRowChanges() {
		if($this->meta === null) {
			return false;
		}
		return $this->meta->useForRowChanges();
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
	public function fillInNewColumns() {
		$_g = 0;
		$_g1 = $this->cmods;
		while($_g < $_g1->length) {
			$cmod = $_g1[$_g];
			++$_g;
			if(!$cmod->rem) {
				if($cmod->add) {
					{
						$_g2 = 0;
						$_g3 = $this->mods;
						while($_g2 < $_g3->length) {
							$mod = $_g3[$_g2];
							++$_g2;
							if($mod->patchRow !== -1 && $mod->destRow !== -1) {
								$d = $this->patch->getCell($cmod->patchRow, $mod->patchRow);
								$this->source->setCell($cmod->destRow, $mod->destRow, $d);
								unset($d);
							}
							unset($mod);
						}
						unset($_g3,$_g2);
					}
					$hdr = $this->header->get($cmod->patchRow);
					$this->source->setCell($cmod->destRow, 0, $this->view->toDatum($hdr));
					unset($hdr);
				}
			}
			unset($cmod);
		}
	}
	public function finishRows() {
		if($this->useMetaForRowChanges()) {
			return;
		}
		if($this->source->get_width() === 0) {
			if($this->source->get_height() !== 0) {
				$this->source->resize(0, 0);
			}
			return;
		}
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
		$this->needDestColumns();
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
							$offset = $this->patchInDestCol->get($c);
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
								$offset1 = $this->patchInDestCol->get($c1);
								if($offset1 !== null && $offset1 >= 0) {
									$this->source->setCell($this->patchInDestCol->get($c1), $mod1->destRow, $d);
								}
								unset($txt,$offset1,$d);
							}
						}
					}
				}
				unset($mod1);
			}
		}
		$this->fillInNewColumns();
		{
			$_g12 = 0;
			$_g3 = $this->source->get_width();
			while($_g12 < $_g3) {
				$i = $_g12++;
				$name = $this->view->toString($this->source->getCell($i, 0));
				$next_name = $this->headerRename->get($name);
				if($next_name === null) {
					continue;
				}
				$this->source->setCell($i, 0, $this->view->toDatum($next_name));
				unset($next_name,$name,$i);
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
		if($this->finished_columns) {
			return;
		}
		$this->finished_columns = true;
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
					$at = -1;
					if($this->patchInSourceCol->exists($i)) {
						$at = $this->patchInSourceCol->get($i);
					}
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
							$at1 = -1;
							if($this->patchInSourceCol->exists($i)) {
								$at1 = $this->patchInSourceCol->get($i);
							}
							$mod2 = new coopy_HighlightPatchUnit();
							$mod2->code = $act;
							$mod2->patchRow = $i;
							$mod2->sourceRow = $at1;
							$this->cmods->push($mod2);
							unset($mod2,$at1);
						}
					}
				}
				unset($i,$hdr,$act);
			}
		}
		$at2 = -1;
		$rat = -1;
		{
			$_g11 = 0;
			$_g2 = $this->cmods->length - 1;
			while($_g11 < $_g2) {
				$i1 = $_g11++;
				$icode = _hx_array_get($this->cmods, $i1)->code;
				if($icode !== "+++" && $icode !== "---") {
					$at2 = _hx_array_get($this->cmods, $i1)->sourceRow;
				}
				_hx_array_get($this->cmods, $i1 + 1)->sourcePrevRow = $at2;
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
				if(!$this->useMetaForColumnChanges()) {
					$this->source->insertOrDeleteColumns($this->colPermutation, $this->colPermutation->length);
				}
			}
		}
		$len = $this->processMods($this->cmods, $fate, $this->source->get_width());
		if(!$this->useMetaForColumnChanges()) {
			$this->source->insertOrDeleteColumns($fate, $len);
			return;
		}
		$changed = false;
		{
			$_g4 = 0;
			$_g13 = $this->cmods;
			while($_g4 < $_g13->length) {
				$mod4 = $_g13[$_g4];
				++$_g4;
				if($mod4->code !== "") {
					$changed = true;
					break;
				}
				unset($mod4);
			}
		}
		if(!$changed) {
			return;
		}
		$columns = new _hx_array(array());
		$target = new haxe_ds_IntMap();
		$inc = array(new _hx_lambda(array(&$at2, &$changed, &$columns, &$fate, &$len, &$rat, &$target), "coopy_HighlightPatch_0"), 'execute');
		{
			$_g14 = 0;
			$_g5 = $fate->length;
			while($_g14 < $_g5) {
				$i2 = $_g14++;
				{
					$value = call_user_func_array($inc, array($fate[$i2]));
					$target->set($i2, $value);
					unset($value);
				}
				unset($i2);
			}
		}
		$this->needSourceColumns();
		$this->needDestColumns();
		{
			$_g15 = 1;
			$_g6 = $this->patch->get_width();
			while($_g15 < $_g6) {
				$idx_patch = $_g15++;
				$change = new coopy_ColumnChange();
				$idx_src = null;
				if($this->patchInSourceCol->exists($idx_patch)) {
					$idx_src = $this->patchInSourceCol->get($idx_patch);
				} else {
					$idx_src = -1;
				}
				$prev_name = null;
				$name = null;
				if($idx_src !== -1) {
					$prev_name = $this->source->getCell($idx_src, 0);
				}
				if($this->modifier->get($idx_patch) !== "---") {
					if($this->header->exists($idx_patch)) {
						$name = $this->header->get($idx_patch);
					}
				}
				$change->prevName = $prev_name;
				$change->name = $name;
				if($this->next_meta !== null) {
					if($this->next_meta->exists($name)) {
						$change->props = $this->next_meta->get($name);
					}
				}
				$columns->push($change);
				unset($prev_name,$name,$idx_src,$idx_patch,$change);
			}
		}
		$this->meta->alterColumns($columns);
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
function coopy_HighlightPatch_0(&$at2, &$changed, &$columns, &$fate, &$len, &$rat, &$target, $x) {
	{
		if($x < 0) {
			return $x;
		} else {
			return $x + 1;
		}
	}
}
