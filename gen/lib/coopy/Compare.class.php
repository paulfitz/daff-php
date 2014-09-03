<?php

class coopy_Compare {
	public function __construct() { if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("coopy.Compare::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}}
	public function compare($parent, $local, $remote, $report) {
		$GLOBALS['%s']->push("coopy.Compare::compare");
		$__hx__spos = $GLOBALS['%s']->length;
		$ws = new coopy_Workspace();
		$ws->parent = $parent;
		$ws->local = $local;
		$ws->remote = $remote;
		$ws->report = $report;
		$report->clear();
		if($parent === null || $local === null || $remote === null) {
			$report->changes->push(new coopy_Change("only 3-way comparison allowed right now"));
			{
				$GLOBALS['%s']->pop();
				return false;
			}
		}
		if($parent->hasStructure() || $local->hasStructure() || $remote->hasStructure()) {
			$tmp = $this->compareStructured($ws);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		{
			$tmp = $this->comparePrimitive($ws);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function compareStructured($ws) {
		$GLOBALS['%s']->push("coopy.Compare::compareStructured");
		$__hx__spos = $GLOBALS['%s']->length;
		$ws->tparent = $ws->parent->getTable();
		$ws->tlocal = $ws->local->getTable();
		$ws->tremote = $ws->remote->getTable();
		if($ws->tparent === null || $ws->tlocal === null || $ws->tremote === null) {
			$ws->report->changes->push(new coopy_Change("structured comparisons that include non-tables are not available yet"));
			{
				$GLOBALS['%s']->pop();
				return false;
			}
		}
		{
			$tmp = $this->compareTable($ws);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function compareTable($ws) {
		$GLOBALS['%s']->push("coopy.Compare::compareTable");
		$__hx__spos = $GLOBALS['%s']->length;
		$ws->p2l = new coopy_TableComparisonState();
		$ws->p2r = new coopy_TableComparisonState();
		$ws->p2l->a = $ws->tparent;
		$ws->p2l->b = $ws->tlocal;
		$ws->p2r->a = $ws->tparent;
		$ws->p2r->b = $ws->tremote;
		$cmp = new coopy_CompareTable();
		$cmp->attach($ws->p2l);
		$cmp->attach($ws->p2r);
		$c = new coopy_Change(null);
		$c->parent = $ws->parent;
		$c->local = $ws->local;
		$c->remote = $ws->remote;
		if($ws->p2l->is_equal && !$ws->p2r->is_equal) {
			$c->mode = coopy_ChangeType::$REMOTE_CHANGE;
		} else {
			if(!$ws->p2l->is_equal && $ws->p2r->is_equal) {
				$c->mode = coopy_ChangeType::$LOCAL_CHANGE;
			} else {
				if(!$ws->p2l->is_equal && !$ws->p2r->is_equal) {
					$ws->l2r = new coopy_TableComparisonState();
					$ws->l2r->a = $ws->tlocal;
					$ws->l2r->b = $ws->tremote;
					$cmp->attach($ws->l2r);
					if($ws->l2r->is_equal) {
						$c->mode = coopy_ChangeType::$SAME_CHANGE;
					} else {
						$c->mode = coopy_ChangeType::$BOTH_CHANGE;
					}
				} else {
					$c->mode = coopy_ChangeType::$NO_CHANGE;
				}
			}
		}
		if((is_object($_t = $c->mode) && !($_t instanceof Enum) ? $_t !== coopy_ChangeType::$NO_CHANGE : $_t != coopy_ChangeType::$NO_CHANGE)) {
			$ws->report->changes->push($c);
		}
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	public function comparePrimitive($ws) {
		$GLOBALS['%s']->push("coopy.Compare::comparePrimitive");
		$__hx__spos = $GLOBALS['%s']->length;
		$sparent = $ws->parent->toString();
		$slocal = $ws->local->toString();
		$sremote = $ws->remote->toString();
		$c = new coopy_Change(null);
		$c->parent = $ws->parent;
		$c->local = $ws->local;
		$c->remote = $ws->remote;
		if($sparent === $slocal && $sparent !== $sremote) {
			$c->mode = coopy_ChangeType::$REMOTE_CHANGE;
		} else {
			if($sparent === $sremote && $sparent !== $slocal) {
				$c->mode = coopy_ChangeType::$LOCAL_CHANGE;
			} else {
				if($slocal === $sremote && $sparent !== $slocal) {
					$c->mode = coopy_ChangeType::$SAME_CHANGE;
				} else {
					if($sparent !== $slocal && $sparent !== $sremote) {
						$c->mode = coopy_ChangeType::$BOTH_CHANGE;
					} else {
						$c->mode = coopy_ChangeType::$NO_CHANGE;
					}
				}
			}
		}
		if((is_object($_t = $c->mode) && !($_t instanceof Enum) ? $_t !== coopy_ChangeType::$NO_CHANGE : $_t != coopy_ChangeType::$NO_CHANGE)) {
			$ws->report->changes->push($c);
		}
		{
			$GLOBALS['%s']->pop();
			return true;
		}
		$GLOBALS['%s']->pop();
	}
	function __toString() { return 'coopy.Compare'; }
}
