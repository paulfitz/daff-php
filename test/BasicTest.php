<?php
class BasicTest extends DaffTestCase {
	
	public function testInit() {

	}

/**
 * Test example of a full array diff to HTML
 *
 * @return void
 */
	public function testSimpleDiff() {
		$data1 = [
			['Country','Capital'],
			['Ireland','Dublin'],
			['France','Paris'],
			['Spain','Barcelona']
		];

		$data2 = [
			['Country','Code','Capital'],
			['Ireland','ie','Dublin'],
			['France','fr','Paris'],
			['Spain','es','Madrid'],
			['Germany','de','Berlin']
		];

		$table1 = new coopy_PhpTableView($data1);
		$table2 = new coopy_PhpTableView($data2);

		$alignment = coopy_Coopy::compareTables($table1, $table2)->align();

		$data_diff = [];
		$table_diff = new coopy_PhpTableView($data_diff);

		$flags = new coopy_CompareFlags();
		$highlighter = new coopy_TableDiff($alignment, $flags);
		$highlighter->hilite($table_diff);

		$diff2html = new coopy_DiffRender();
		$diff2html->usePrettyArrows(false);
		$diff2html->render($table_diff);
		$table_diff_html = $diff2html->html();

		$expected = array(
			'table' => array(),
				array('tr' => array('class' => 'spec')),
					array('td' => true), '!', '/td',
					array('td' => true), '/td',
					array('td' => array('class' => 'add')), '+++', '/td',
					array('td' => true), '/td',
				'/tr',
				array('tr' => true),
					array('th' => true), '@@', '/th',
					array('th' => true), 'Country', '/th',
					array('th' => array('class' => 'add')), 'Code', '/th',
					array('th' => true), 'Capital', '/th',
				'/tr',
				array('tr' => true),
					array('td' => true), '+', '/td',
					array('td' => true), 'Ireland', '/td',
					array('td' => array('class' => 'add')), 'ie', '/td',
					array('td' => true), 'Dublin', '/td',
				'/tr',
				array('tr' => true),
					array('td' => true), '+', '/td',
					array('td' => true), 'France', '/td',
					array('td' => array('class' => 'add')), 'fr', '/td',
					array('td' => true), 'Paris', '/td',
				'/tr',
				array('tr' => array('class' => 'modify')),
					array('td' => array('class' => 'modify')), '->', '/td',
					array('td' => true), 'Spain', '/td',
					array('td' => array('class' => 'add')), 'es', '/td',
					array('td' => array('class' => 'modify')), 'Barcelona->Madrid', '/td',
				'/tr',
				array('tr' => array('class' => 'add')),
					array('td' => true), '+++', '/td',
					array('td' => true), 'Germany', '/td',
					array('td' => array('class' => 'add')), 'de', '/td',
					array('td' => true), 'Berlin', '/td',
				'/tr',
			'/table',
		);
		static::assertHtml($expected, $table_diff_html);


		$patcher = new coopy_HighlightPatch($table1,$table_diff);
		$patcher->apply();
	}
}