<?php

class coopy_ChangeType extends Enum {
	public static $BOTH_CHANGE;
	public static $LOCAL_CHANGE;
	public static $NOTE_CHANGE;
	public static $NO_CHANGE;
	public static $REMOTE_CHANGE;
	public static $SAME_CHANGE;
	public static $__constructors = array(3 => 'BOTH_CHANGE', 2 => 'LOCAL_CHANGE', 5 => 'NOTE_CHANGE', 0 => 'NO_CHANGE', 1 => 'REMOTE_CHANGE', 4 => 'SAME_CHANGE');
	}
coopy_ChangeType::$BOTH_CHANGE = new coopy_ChangeType("BOTH_CHANGE", 3);
coopy_ChangeType::$LOCAL_CHANGE = new coopy_ChangeType("LOCAL_CHANGE", 2);
coopy_ChangeType::$NOTE_CHANGE = new coopy_ChangeType("NOTE_CHANGE", 5);
coopy_ChangeType::$NO_CHANGE = new coopy_ChangeType("NO_CHANGE", 0);
coopy_ChangeType::$REMOTE_CHANGE = new coopy_ChangeType("REMOTE_CHANGE", 1);
coopy_ChangeType::$SAME_CHANGE = new coopy_ChangeType("SAME_CHANGE", 4);
