<?php
class BasicTest extends DaffTestCase {
	
	public function testInit() {

	}

	protected function _quickDiff($data1, $data2) {
		$table1 = new coopy_PhpTableView($data1);
		$table2 = new coopy_PhpTableView($data2);

		$data_diff = [];
		$table_diff = new coopy_PhpTableView($data_diff);

		$highlighter = new coopy_TableDiff(coopy_Coopy::compareTables($table1, $table2)->align(), new coopy_CompareFlags());
		$highlighter->hilite($table_diff);

		$diff2html = new coopy_DiffRender();
		$diff2html->usePrettyArrows(false);
		$diff2html->render($table_diff);

		return $diff2html->html();
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
		$result = $this->_quickDiff($data1, $data2);

		$expected = array(
			'table' => array(),
            'thead' => array(),
				array('tr' => array('class' => 'spec')),
					array('td' => true), '!', '/td',
					array('td' => true), '/td',
					array('td' => array('class' => 'add')), '+++', '/td',
					array('td' => true), '/td',
				'/tr',
				array('tr' => array('class' => 'header')),
					array('th' => true), '@@', '/th',
					array('th' => true), 'Country', '/th',
					array('th' => array('class' => 'add')), 'Code', '/th',
					array('th' => true), 'Capital', '/th',
				'/tr',
            '/thead',
            'tbody' => array(),
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
			'/tbody',
			'/table',
		);
		static::assertHtml($expected, $result);
	}

/**
 * test adding all new elements
 *
 * @return void
 */
	public function testAllNew() {
		$data2 = [
			['Country','Code','Capital'],
			['Ireland','ie','Dublin'],
		];

		$result = $this->_quickDiff([], $data2);

		$expected = array(
			'table' => array(),
            'thead' => array(),
				array('tr' => array('class' => 'spec')),
					array('td' => true), '!', '/td',
					array('td' => array('class' => 'add')), '+++', '/td',
					array('td' => array('class' => 'add')), '+++', '/td',
					array('td' => array('class' => 'add')), '+++', '/td',
				'/tr',
                array('tr' => array('class' => 'header')),
					array('th' => true), '@@', '/th',
					array('th' => array('class' => 'add')), 'Country', '/th',
					array('th' => array('class' => 'add')), 'Code', '/th',
					array('th' => array('class' => 'add')), 'Capital', '/th',
				'/tr',
            '/thead',
            'tbody' => array(),
				array('tr' => array('class' => 'add')),
					array('td' => true), '+++', '/td',
					array('td' => array('class' => 'add')), 'Ireland', '/td',
					array('td' => array('class' => 'add')), 'ie', '/td',
					array('td' => array('class' => 'add')), 'Dublin', '/td',
				'/tr',
            '/tbody',
			'/table',
		);
		static::assertHtml($expected, $result);
	}

/**
 * test removing all elements
 *
 * @return void
 */
	public function testAllRemoved() {
		$data1 = [
			['Country','Code','Capital'],
			['Ireland','ie','Dublin'],
		];

		$result = $this->_quickDiff($data1, []);

		$expected = array(
			'table' => array(),
            'thead' => array(),
				array('tr' => array('class' => 'spec')),
					array('td' => true), '!', '/td',
					array('td' => array('class' => 'remove')), '---', '/td',
					array('td' => array('class' => 'remove')), '---', '/td',
					array('td' => array('class' => 'remove')), '---', '/td',
				'/tr',
                array('tr' => array('class' => 'header')),
					array('th' => true), '@@', '/th',
					array('th' => array('class' => 'remove')), 'Country', '/th',
					array('th' => array('class' => 'remove')), 'Code', '/th',
					array('th' => array('class' => 'remove')), 'Capital', '/th',
				'/tr',
            '/thead',
            'tbody' => array(),
				array('tr' => array('class' => 'remove')),
					array('td' => true), '---', '/td',
					array('td' => array('class' => 'remove')), 'Ireland', '/td',
					array('td' => array('class' => 'remove')), 'ie', '/td',
					array('td' => array('class' => 'remove')), 'Dublin', '/td',
				'/tr',
            '/tbody',
			'/table',
		);
		static::assertHtml($expected, $result);
	}
}
