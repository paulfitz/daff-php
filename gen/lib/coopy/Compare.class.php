<?php

class coopy_Compare {
	public function __construct() {}
	public function compare($parent, $local, $remote, $report) { if(!php_Boot::$skip_constructor) {
		$ws = new coopy_Workspace();
		$ws->parent = $parent;
		$ws->local = $local;
		$ws->remote = $remote;
		$ws->report = $report;
		$report->clear();
		if($parent === null || $local === null || $remote === null) {
			$report->changes->push(new coopy_Change("only 3-way comparison allowed right now"));
			return false;
		}
		if($parent->hasStructure() || $local->hasStructure() || $remote->hasStructure()) {
			return $this->compareStructured($ws);
		}
		return $this->comparePrimitive($ws);
	}}
	public function compareStructured($ws) {
		$ws->tparent = $ws->parent->getTable();
		$ws->tlocal = $ws->local->getTable();
		$ws->tremote = $ws->remote->getTable();
		if($ws->tparent === null || $ws->tlocal === null || $ws->tremote === null) {
			$ws->report->changes->push(new coopy_Change("structured comparisons that include non-tables are not available yet"));
			return false;
		}
		return $this->compareTable($ws);
	}
	public function compareTable($ws) {
		$ws->p2l = new coopy_TableComparisonState();
		$ws->p2r = new coopy_TableComparisonState();
		$ws->p2l->a = $ws->tparent;
		$ws->p2l->b = $ws->tlocal;
		$ws->p2r->a = $ws->tparent;
		$ws->p2r->b = $ws->tremote;
		$cmpl = new coopy_CompareTable($ws->p2l);
		$cmpl->run();
		$cmpr = new coopy_CompareTable($ws->p2r);
		$cmpl->run();
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
					$cmp = new coopy_CompareTable($ws->l2r);
					$cmp->run();
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
		return true;
	}
	public function comparePrimitive($ws) {
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
		return true;
	}
	function __toString() { return 'coopy.Compare'; }
}
