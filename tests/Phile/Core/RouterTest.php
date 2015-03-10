<?php

namespace PhileTest\Core;

use Phile\Core\Registry;
use Phile\Core\Router;

/**
 * the Router class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class RouterTest extends \PHPUnit_Framework_TestCase {

	protected $settings;

	protected function setUp() {
		$this->settings = Registry::get('Phile_Settings');
		parent::setup();
	}

	protected function tearDown() {
		Registry::set('Phile_Settings', $this->settings);
		unset($this->settings);
	}

	public function testUrlForPageFull() {
		$this->mockBaseUrl('http://barbaz');
		$page = 'index/foo';
		$expected = 'http://barbaz/index/foo';
		$result = Router::urlForPage($page);
		$this->assertEquals($expected, $result);
	}

	public function testUrlForPageRelative() {
		$page = 'index/foo';
		$expected = 'index/foo';
		$result = Router::urlForPage($page, false);
		$this->assertEquals($expected, $result);
	}

	public function testUrl() {
		$this->mockBaseUrl('http://barbaz');
		$expected = 'http://barbaz/sub/page';
		$result = Router::url('sub/page');
		$this->assertEquals($expected, $result);
	}

	public function testGetBaseUrl() {
		$this->mockBaseUrl(null);
		// http://foo/bar/index.php
		$_SERVER['PHP_SELF'] = '/bar/index.php';
		$_SERVER['HTTP_HOST'] = 'foo';

		$this->assertEquals('http://foo/bar', Router::getBaseUrl());
	}

	public function testGetBaseUrlPreset() {
		$this->mockBaseUrl('https://barbaz');
		$this->assertEquals('https://barbaz', Router::getBaseUrl());
	}

	public function testTidyUrl() {
		$this->assertEquals('', Router::tidyUrl('index'));
		$this->assertEquals('', Router::tidyUrl('index/'));

		$this->assertEquals('foo-index', Router::tidyUrl('foo-index/'));
		$this->assertEquals('foo-index', Router::tidyUrl('foo-index'));

		$this->assertEquals('sub', Router::tidyUrl('sub/'));
		$this->assertEquals('sub/page', Router::tidyUrl('sub/page/'));
	}

	public function mockBaseUrl($url) {
		Registry::set(
			'Phile_Settings',
			['base_url' => $url] + $this->settings
		);
	}

}
