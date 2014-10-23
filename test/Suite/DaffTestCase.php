<?php
class DaffTestCase extends PHPUnit_Framework_TestCase {

/**
 * Setup the test case
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
	}

/**
 * teardown 
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
	}

/**
 * Asserts HTML tags.
 *
 * Takes an array $expected and generates a regex from it to match the provided $string.
 * Samples for $expected:
 *
 * Checks for an input tag with a name attribute (contains any non-empty value) and an id
 * attribute that contains 'my-input':
 *
 * {{{
 * array('input' => array('name', 'id' => 'my-input'))
 * }}}
 *
 * Checks for two p elements with some text in them:
 *
 * {{{
 * array(
 *   array('p' => true),
 *   'textA',
 *   '/p',
 *   array('p' => true),
 *   'textB',
 *   '/p'
 * )
 * }}}
 *
 * You can also specify a pattern expression as part of the attribute values, or the tag
 * being defined, if you prepend the value with preg: and enclose it with slashes, like so:
 *
 * {{{
 * array(
 *   array('input' => array('name', 'id' => 'preg:/FieldName\d+/')),
 *   'preg:/My\s+field/'
 * )
 * }}}
 *
 * Important: This function is very forgiving about whitespace and also accepts any
 * permutation of attribute order. It will also allow whitespace between specified tags.
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see 
 * https://github.com/cakephp/cakephp/blob/master/lib/Cake/LICENSE.txt
 *
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * @param array $expected An array, see above
 * @param string $string An HTML/XHTML/XML string
 * @param string $fullDebug Whether or not more verbose output should be used.
 * @return void
 */
	public function assertHtml($expected, $string, $fullDebug = false) {
		$regex = array();
		$normalized = array();
		foreach ((array)$expected as $key => $val) {
			if (!is_numeric($key)) {
				$normalized[] = array($key => $val);
			} else {
				$normalized[] = $val;
			}
		}
		$i = 0;
		foreach ($normalized as $tags) {
			if (!is_array($tags)) {
				$tags = (string)$tags;
			}
			$i++;
			if (is_string($tags) && $tags{0} === '<') {
				$tags = array(substr($tags, 1) => array());
			} elseif (is_string($tags)) {
				$tagsTrimmed = preg_replace('/\s+/m', '', $tags);

				if (preg_match('/^\*?\//', $tags, $match) && $tagsTrimmed !== '//') {
					$prefix = array(null, null);

					if ($match[0] === '*/') {
						$prefix = array('Anything, ', '.*?');
					}
					$regex[] = array(
						sprintf('%sClose %s tag', $prefix[0], substr($tags, strlen($match[0]))),
						sprintf('%s<[\s]*\/[\s]*%s[\s]*>[\n\r]*', $prefix[1], substr($tags, strlen($match[0]))),
						$i,
					);
					continue;
				}
				if (!empty($tags) && preg_match('/^preg\:\/(.+)\/$/i', $tags, $matches)) {
					$tags = $matches[1];
					$type = 'Regex matches';
				} else {
					$tags = preg_quote($tags, '/');
					$type = 'Text equals';
				}
				$regex[] = array(
					sprintf('%s "%s"', $type, $tags),
					$tags,
					$i,
				);
				continue;
			}
			foreach ($tags as $tag => $attributes) {
				$regex[] = array(
					sprintf('Open %s tag', $tag),
					sprintf('[\s]*<%s', preg_quote($tag, '/')),
					$i,
				);
				if ($attributes === true) {
					$attributes = array();
				}
				$attrs = array();
				$explanations = array();
				$i = 1;
				foreach ($attributes as $attr => $val) {
					if (is_numeric($attr) && preg_match('/^preg\:\/(.+)\/$/i', $val, $matches)) {
						$attrs[] = $matches[1];
						$explanations[] = sprintf('Regex "%s" matches', $matches[1]);
						continue;
					} else {
						$quotes = '["\']';
						if (is_numeric($attr)) {
							$attr = $val;
							$val = '.+?';
							$explanations[] = sprintf('Attribute "%s" present', $attr);
						} elseif (!empty($val) && preg_match('/^preg\:\/(.+)\/$/i', $val, $matches)) {
							$val = str_replace(
								array('.*', '.+'),
								array('.*?', '.+?'),
								$matches[1]
							);
							$quotes = $val !== $matches[1] ? '["\']' : '["\']?';

							$explanations[] = sprintf('Attribute "%s" matches "%s"', $attr, $val);
						} else {
							$explanations[] = sprintf('Attribute "%s" == "%s"', $attr, $val);
							$val = preg_quote($val, '/');
						}
						$attrs[] = '[\s]+' . preg_quote($attr, '/') . '=' . $quotes . $val . $quotes;
					}
					$i++;
				}
				if ($attrs) {
					$regex[] = array(
						'explains' => $explanations,
						'attrs' => $attrs,
					);
				}
				$regex[] = array(
					sprintf('End %s tag', $tag),
					'[\s]*\/?[\s]*>[\n\r]*',
					$i,
				);
			}
		}
		foreach ($regex as $i => $assertion) {
			$matches = false;
			if (isset($assertion['attrs'])) {
				$string = $this->_assertAttributes($assertion, $string);
				continue;
			}

			list($description, $expressions, $itemNum) = $assertion;
			foreach ((array)$expressions as $expression) {
				$expression = sprintf('/^%s/s', $expression);
				if (preg_match($expression, $string, $match)) {
					$matches = true;
					$string = substr($string, strlen($match[0]));
					break;
				}
			}
			if (!$matches) {
				$this->assertRegExp($expression, $string, sprintf('Item #%d / regex #%d failed: %s', $itemNum, $i, $description));
				if ($fullDebug) {
					debug($string, true);
					debug($regex, true);
				}
				return false;
			}
		}

		$this->assertTrue(true, '%s');
		return true;
	}

/**
 * Check the attributes as part of an assertTags() check.
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see 
 * https://github.com/cakephp/cakephp/blob/master/lib/Cake/LICENSE.txt
 *
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * @param array $assertions Assertions to run.
 * @param string $string The HTML string to check.
 * @return void
 */
	protected function _assertAttributes($assertions, $string) {
		$asserts = $assertions['attrs'];
		$explains = $assertions['explains'];
		$len = count($asserts);
		do {
			$matches = false;
			foreach ($asserts as $j => $assert) {
				if (preg_match(sprintf('/^%s/s', $assert), $string, $match)) {
					$matches = true;
					$string = substr($string, strlen($match[0]));
					array_splice($asserts, $j, 1);
					array_splice($explains, $j, 1);
					break;
				}
			}
			if ($matches === false) {
				$this->assertTrue(false, 'Attribute did not match. Was expecting ' . $explains[$j]);
			}
			$len = count($asserts);
		} while ($len > 0);
		return $string;
	}
	
}