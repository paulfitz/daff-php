<?php

class coopy_TerminalDiffRender {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.TerminalDiffRender::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}}
	public function render($t) {
		$GLOBALS['%s']->push("coopy.TerminalDiffRender::render");
		$__hx__spos = $GLOBALS['%s']->length;
		$csv = new coopy_Csv(null);
		$result = "";
		$w = $t->get_width();
		$h = $t->get_height();
		$txt = "";
		$v = $t->getCellView();
		$tt = new coopy_TableText($t);
		$codes = new haxe_ds_StringMap();
		$codes->set("header", "\x1B[0;1m");
		$codes->set("spec", "\x1B[35;1m");
		$codes->set("add", "\x1B[32;1m");
		$codes->set("conflict", "\x1B[33;1m");
		$codes->set("modify", "\x1B[34;1m");
		$codes->set("remove", "\x1B[31;1m");
		$codes->set("minor", "\x1B[2m");
		$codes->set("done", "\x1B[0m");
		{
			$_g = 0;
			while($_g < $h) {
				$y = $_g++;
				{
					$_g1 = 0;
					while($_g1 < $w) {
						$x = $_g1++;
						if($x > 0) {
							$txt .= _hx_string_or_null($codes->get("minor")) . "," . _hx_string_or_null($codes->get("done"));
						}
						$val = $tt->getCellText($x, $y);
						if($val === null) {
							$val = "";
						}
						$cell = coopy_DiffRender::renderCell($tt, $x, $y);
						$code = null;
						if($cell->category !== null) {
							$code = $codes->get($cell->category);
						}
						if($cell->category_given_tr !== null) {
							$code_tr = $codes->get($cell->category_given_tr);
							if($code_tr !== null) {
								$code = $code_tr;
							}
							unset($code_tr);
						}
						if($code !== null) {
							if($cell->rvalue !== null) {
								$val = _hx_string_or_null($codes->get("remove")) . _hx_string_or_null($cell->lvalue) . _hx_string_or_null($codes->get("modify")) . _hx_string_or_null($cell->pretty_separator) . _hx_string_or_null($codes->get("add")) . _hx_string_or_null($cell->rvalue) . _hx_string_or_null($codes->get("done"));
								if($cell->pvalue !== null) {
									$val = _hx_string_or_null($codes->get("conflict")) . _hx_string_or_null($cell->pvalue) . _hx_string_or_null($codes->get("modify")) . _hx_string_or_null($cell->pretty_separator) . _hx_string_or_null($val);
								}
							} else {
								$val = $cell->pretty_value;
								$val = _hx_string_or_null($code) . _hx_string_or_null($val) . _hx_string_or_null($codes->get("done"));
							}
						}
						$txt .= _hx_string_or_null($csv->renderCell($v, $val));
						unset($x,$val,$code,$cell);
					}
					unset($_g1);
				}
				$txt .= "\x0D\x0A";
				unset($y);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $txt;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'coopy.TerminalDiffRender'; }
}
