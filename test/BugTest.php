<?php
class BugTest extends DaffTestCase {
	
/**
 * Test rows are ordered correctly when a row is removed
 */
	public function testIssue11() {
		$data1 = [
			['Country','Capital'],
			['Ireland','Dublin'],
			['France','Paris'],
			['Spain','Barcelona']
		];

		$data2 = [
			['Country','Capital'],
			['Ireland','Dublin'],
			['Spain','Madrid'],
		];

		$table1 = new coopy_PhpTableView($data1);
		$table2 = new coopy_PhpTableView($data2);

		$data_diff = [];
		$table_diff = new coopy_PhpTableView($data_diff);

		$highlighter = new coopy_TableDiff(coopy_Coopy::compareTables($table1, $table2)->align(), new coopy_CompareFlags());
		$highlighter->hilite($table_diff);

		foreach ($table_diff->data as $row) {
			$sortedRow = $row;
			ksort($sortedRow, SORT_NUMERIC);
			$this->assertEquals($row, $sortedRow);
		}
	}
}