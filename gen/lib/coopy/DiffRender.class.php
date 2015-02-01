<?php

class coopy_DiffRender {
	public function __construct() {
		if(!php_Boot::$skip_constructor) {
		$this->text_to_insert = new _hx_array(array());
		$this->open = false;
		$this->pretty_arrows = true;
	}}
	public $text_to_insert;
	public $td_open;
	public $td_close;
	public $open;
	public $pretty_arrows;
	public $section;
	public function usePrettyArrows($flag) {
		$this->pretty_arrows = $flag;
	}
	public function insert($str) {
		$this->text_to_insert->push($str);
	}
	public function beginTable() {
		$this->insert("<table>\x0A");
		$this->section = null;
	}
	public function setSection($str) {
		if($str === $this->section) {
			return;
		}
		if($this->section !== null) {
			$this->insert("</t");
			$this->insert($this->section);
			$this->insert(">\x0A");
		}
		$this->section = $str;
		if($this->section !== null) {
			$this->insert("<t");
			$this->insert($this->section);
			$this->insert(">\x0A");
		}
	}
	public function beginRow($mode) {
		$this->td_open = "<td";
		$this->td_close = "</td>";
		$row_class = "";
		if($mode === "header") {
			$this->td_open = "<th";
			$this->td_close = "</th>";
		}
		$row_class = $mode;
		$tr = "<tr>";
		if($row_class !== "") {
			$tr = "<tr class=\"" . _hx_string_or_null($row_class) . "\">";
		}
		$this->insert($tr);
	}
	public function insertCell($txt, $mode) {
		$cell_decorate = "";
		if($mode !== "") {
			$cell_decorate = " class=\"" . _hx_string_or_null($mode) . "\"";
		}
		$this->insert(_hx_string_or_null($this->td_open) . _hx_string_or_null($cell_decorate) . ">");
		$this->insert($txt);
		$this->insert($this->td_close);
	}
	public function endRow() {
		$this->insert("</tr>\x0A");
	}
	public function endTable() {
		$this->setSection(null);
		$this->insert("</table>\x0A");
	}
	public function html() {
		return $this->text_to_insert->join("");
	}
	public function toString() {
		return $this->html();
	}
	public function render($tab) {
		if($tab->get_width() === 0 || $tab->get_height() === 0) {
			return $this;
		}
		$render = $this;
		$render->beginTable();
		$change_row = -1;
		$cell = new coopy_CellInfo();
		$view = $tab->getCellView();
		$corner = $view->toString($tab->getCell(0, 0));
		$off = null;
		if($corner === "@:@") {
			$off = 1;
		} else {
			$off = 0;
		}
		if($off > 0) {
			if($tab->get_width() <= 1 || $tab->get_height() <= 1) {
				return $this;
			}
		}
		{
			$_g1 = 0;
			$_g = $tab->get_height();
			while($_g1 < $_g) {
				$row = $_g1++;
				$open = false;
				$txt = $view->toString($tab->getCell($off, $row));
				if($txt === null) {
					$txt = "";
				}
				coopy_DiffRender::examineCell($off, $row, $view, $txt, "", $txt, $corner, $cell, $off);
				$row_mode = $cell->category;
				if($row_mode === "spec") {
					$change_row = $row;
				}
				if($row_mode === "header" || $row_mode === "spec" || $row_mode === "index") {
					$this->setSection("head");
				} else {
					$this->setSection("body");
				}
				$render->beginRow($row_mode);
				{
					$_g3 = 0;
					$_g2 = $tab->get_width();
					while($_g3 < $_g2) {
						$c = $_g3++;
						coopy_DiffRender::examineCell($c, $row, $view, $tab->getCell($c, $row), (($change_row >= 0) ? $view->toString($tab->getCell($c, $change_row)) : ""), $txt, $corner, $cell, $off);
						$render->insertCell(coopy_DiffRender_0($this, $_g, $_g1, $_g2, $_g3, $c, $cell, $change_row, $corner, $off, $open, $render, $row, $row_mode, $tab, $txt, $view), $cell->category_given_tr);
						unset($c);
					}
					unset($_g3,$_g2);
				}
				$render->endRow();
				unset($txt,$row_mode,$row,$open);
			}
		}
		$render->endTable();
		return $this;
	}
	public function sampleCss() {
		return ".highlighter .add { \x0A  background-color: #7fff7f;\x0A}\x0A\x0A.highlighter .remove { \x0A  background-color: #ff7f7f;\x0A}\x0A\x0A.highlighter td.modify { \x0A  background-color: #7f7fff;\x0A}\x0A\x0A.highlighter td.conflict { \x0A  background-color: #f00;\x0A}\x0A\x0A.highlighter .spec { \x0A  background-color: #aaa;\x0A}\x0A\x0A.highlighter .move { \x0A  background-color: #ffa;\x0A}\x0A\x0A.highlighter .null { \x0A  color: #888;\x0A}\x0A\x0A.highlighter table { \x0A  border-collapse:collapse;\x0A}\x0A\x0A.highlighter td, .highlighter th {\x0A  border: 1px solid #2D4068;\x0A  padding: 3px 7px 2px;\x0A}\x0A\x0A.highlighter th, .highlighter .header { \x0A  background-color: #aaf;\x0A  font-weight: bold;\x0A  padding-bottom: 4px;\x0A  padding-top: 5px;\x0A  text-align:left;\x0A}\x0A\x0A.highlighter tr.header th {\x0A  border-bottom: 2px solid black;\x0A}\x0A\x0A.highlighter tr.index td, .highlighter .index, .highlighter tr.header th.index {\x0A  background-color: white;\x0A  border: none;\x0A}\x0A\x0A.highlighter .gap {\x0A  color: #888;\x0A}\x0A\x0A.highlighter td {\x0A  empty-cells: show;\x0A}\x0A";
	}
	public function completeHtml() {
		$this->text_to_insert->insert(0, "<!DOCTYPE html>\x0A<html>\x0A<head>\x0A<meta charset='utf-8'>\x0A<style TYPE='text/css'>\x0A");
		$this->text_to_insert->insert(1, $this->sampleCss());
		$this->text_to_insert->insert(2, "</style>\x0A</head>\x0A<body>\x0A<div class='highlighter'>\x0A");
		$this->text_to_insert->push("</div>\x0A</body>\x0A</html>\x0A");
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
	static function examineCell($x, $y, $view, $raw, $vcol, $vrow, $vcorner, $cell, $offset = null) {
		if($offset === null) {
			$offset = 0;
		}
		$nested = $view->isHash($raw);
		$value = null;
		if(!$nested) {
			$value = $view->toString($raw);
		}
		$cell->category = "";
		$cell->category_given_tr = "";
		$cell->separator = "";
		$cell->pretty_separator = "";
		$cell->conflicted = false;
		$cell->updated = false;
		$cell->pvalue = $cell->lvalue = $cell->rvalue = null;
		$cell->value = $value;
		if($cell->value === null) {
			$cell->value = "";
		}
		$cell->pretty_value = $cell->value;
		if($vrow === null) {
			$vrow = "";
		}
		if($vcol === null) {
			$vcol = "";
		}
		$removed_column = false;
		if($vrow === ":") {
			$cell->category = "move";
		}
		if($vrow === "" && $offset === 1 && $y === 0) {
			$cell->category = "index";
		}
		if(_hx_index_of($vcol, "+++", null) >= 0) {
			$cell->category_given_tr = $cell->category = "add";
		} else {
			if(_hx_index_of($vcol, "---", null) >= 0) {
				$cell->category_given_tr = $cell->category = "remove";
				$removed_column = true;
			}
		}
		if($vrow === "!") {
			$cell->category = "spec";
		} else {
			if($vrow === "@@") {
				$cell->category = "header";
			} else {
				if($vrow === "...") {
					$cell->category = "gap";
				} else {
					if($vrow === "+++") {
						if(!$removed_column) {
							$cell->category = "add";
						}
					} else {
						if($vrow === "---") {
							$cell->category = "remove";
						} else {
							if(_hx_index_of($vrow, "->", null) >= 0) {
								if(!$removed_column) {
									$tokens = _hx_explode("!", $vrow);
									$full = $vrow;
									$part = $tokens[1];
									if($part === null) {
										$part = $full;
									}
									if($nested || _hx_index_of($cell->value, $part, null) >= 0) {
										$cat = "modify";
										$div = $part;
										if($part !== $full) {
											if($nested) {
												$cell->conflicted = $view->hashExists($raw, "theirs");
											} else {
												$cell->conflicted = _hx_index_of($cell->value, $full, null) >= 0;
											}
											if($cell->conflicted) {
												$div = $full;
												$cat = "conflict";
											}
										}
										$cell->updated = true;
										$cell->separator = $div;
										$cell->pretty_separator = $div;
										if($nested) {
											if($cell->conflicted) {
												$tokens = (new _hx_array(array($view->hashGet($raw, "before"), $view->hashGet($raw, "ours"), $view->hashGet($raw, "theirs"))));
											} else {
												$tokens = (new _hx_array(array($view->hashGet($raw, "before"), $view->hashGet($raw, "after"))));
											}
										} else {
											if($cell->pretty_value === $div) {
												$tokens = (new _hx_array(array("", "")));
											} else {
												$tokens = _hx_explode($div, $cell->pretty_value);
											}
										}
										$pretty_tokens = $tokens;
										if($tokens->length >= 2) {
											$pretty_tokens[0] = coopy_DiffRender::markSpaces($tokens[0], $tokens[1]);
											$pretty_tokens[1] = coopy_DiffRender::markSpaces($tokens[1], $tokens[0]);
										}
										if($tokens->length >= 3) {
											$ref = $pretty_tokens[0];
											$pretty_tokens[0] = coopy_DiffRender::markSpaces($ref, $tokens[2]);
											$pretty_tokens[2] = coopy_DiffRender::markSpaces($tokens[2], $ref);
										}
										$cell->pretty_separator = chr(8594);
										$cell->pretty_value = $pretty_tokens->join($cell->pretty_separator);
										$cell->category_given_tr = $cell->category = $cat;
										$offset1 = null;
										if($cell->conflicted) {
											$offset1 = 1;
										} else {
											$offset1 = 0;
										}
										$cell->lvalue = $tokens[$offset1];
										$cell->rvalue = $tokens[$offset1 + 1];
										if($cell->conflicted) {
											$cell->pvalue = $tokens[0];
										}
									}
								}
							}
						}
					}
				}
			}
		}
		if($x === 0 && $offset > 0) {
			$cell->category_given_tr = $cell->category = "index";
		}
	}
	static function markSpaces($sl, $sr) {
		if($sl === $sr) {
			return $sl;
		}
		if($sl === null || $sr === null) {
			return $sl;
		}
		$slc = str_replace(" ", "", $sl);
		$src = str_replace(" ", "", $sr);
		if($slc !== $src) {
			return $sl;
		}
		$slo = "";
		$il = 0;
		$ir = 0;
		while($il < strlen($sl)) {
			$cl = _hx_char_at($sl, $il);
			$cr = "";
			if($ir < strlen($sr)) {
				$cr = _hx_char_at($sr, $ir);
			}
			if($cl === $cr) {
				$slo .= _hx_string_or_null($cl);
				$il++;
				$ir++;
			} else {
				if($cr === " ") {
					$ir++;
				} else {
					$slo .= _hx_string_or_null(chr(9251));
					$il++;
				}
			}
			unset($cr,$cl);
		}
		return $slo;
	}
	static function renderCell($tab, $view, $x, $y) {
		$cell = new coopy_CellInfo();
		$corner = $view->toString($tab->getCell(0, 0));
		$off = null;
		if($corner === "@:@") {
			$off = 1;
		} else {
			$off = 0;
		}
		coopy_DiffRender::examineCell($x, $y, $view, $tab->getCell($x, $y), $view->toString($tab->getCell($x, $off)), $view->toString($tab->getCell($off, $y)), $corner, $cell, $off);
		return $cell;
	}
	function __toString() { return $this->toString(); }
}
function coopy_DiffRender_0(&$__hx__this, &$_g, &$_g1, &$_g2, &$_g3, &$c, &$cell, &$change_row, &$corner, &$off, &$open, &$render, &$row, &$row_mode, &$tab, &$txt, &$view) {
	if($__hx__this->pretty_arrows) {
		return $cell->pretty_value;
	} else {
		return $cell->value;
	}
}
