<?php

class coopy_TableIO {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.TableIO::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}}
	public function getContent($name) {
		$GLOBALS['%s']->push("coopy.TableIO::getContent");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = sys_io_File::getContent($name);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function saveContent($name, $txt) {
		$GLOBALS['%s']->push("coopy.TableIO::saveContent");
		$__hx__spos = $GLOBALS['%s']->length;
		sys_io_File::saveContent($name, $txt);
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	public function args() {
		$GLOBALS['%s']->push("coopy.TableIO::args");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = Sys::args();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function writeStdout($txt) {
		$GLOBALS['%s']->push("coopy.TableIO::writeStdout");
		$__hx__spos = $GLOBALS['%s']->length;
		Sys::stdout()->writeString($txt);
		$GLOBALS['%s']->pop();
	}
	public function writeStderr($txt) {
		$GLOBALS['%s']->push("coopy.TableIO::writeStderr");
		$__hx__spos = $GLOBALS['%s']->length;
		Sys::stderr()->writeString($txt);
		$GLOBALS['%s']->pop();
	}
	public function command($cmd, $args) {
		$GLOBALS['%s']->push("coopy.TableIO::command");
		$__hx__spos = $GLOBALS['%s']->length;
		try {
			{
				$tmp = Sys::command($cmd, $args);
				$GLOBALS['%s']->pop();
				return $tmp;
			}
		}catch(Exception $__hx__e) {
			$_ex_ = ($__hx__e instanceof HException) ? $__hx__e->e : $__hx__e;
			$e = $_ex_;
			{
				$GLOBALS['%e'] = (new _hx_array(array()));
				while($GLOBALS['%s']->length >= $__hx__spos) {
					$GLOBALS['%e']->unshift($GLOBALS['%s']->pop());
				}
				$GLOBALS['%s']->push($GLOBALS['%e'][0]);
				{
					$GLOBALS['%s']->pop();
					return 1;
				}
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function async() {
		$GLOBALS['%s']->push("coopy.TableIO::async");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$GLOBALS['%s']->pop();
			return false;
		}
		$GLOBALS['%s']->pop();
	}
	public function exists($path) {
		$GLOBALS['%s']->push("coopy.TableIO::exists");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = file_exists($path);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'coopy.TableIO'; }
}
