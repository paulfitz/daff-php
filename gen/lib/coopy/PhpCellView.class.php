<?php

class coopy_PhpCellView implements coopy_View {
  public function toString($d) {
    return print_r($d,true);
  }
  public function equals($d1,$d2) {
      return "".$d1 == "".$d2;
  }
  public function toDatum($d) { return $d; }
  public function makeHash() { return array(); }
  public function isHash($d) { return is_array($d); }
  public function hashSet(&$d,$k,$v) { $d[$k] = $v; }
  public function hashGet($d,$k) { return $d[$k]; }
  public function hashExists($d,$k) { return array_key_exists($k,$d); }
  public function isTable($t) { return false; }
  public function getTable($t) { return false; }
  public function wrapTable($t) { return false; }
}
