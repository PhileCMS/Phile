<?php

namespace PhileTest\Core;

use Phile\Bootstrap;
use Phile\Core\Registry;
use Phile\Core\Request;

/**
 * the ResponseTest class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class RequestTest extends \PHPUnit_Framework_TestCase {

	protected $baseUrl = 'http://foo';

	public function setUp() {
		Bootstrap::getInstance()->initializeBasics();
		$settings = Registry::get('Phile_Settings');
		Registry::set(
			'Phile_Settings',
			['base_url' => $this->baseUrl] + $settings
		);
		parent::setUp();
	}

	public function testGetData() {
		$this->assertNull(Request::getData('zup'));

		$_GET['foo'] = 'bar';
		$this->assertEquals(Request::getData('foo'), 'bar');

		$_GET['baz'] = 'zap';
		$this->assertEquals(Request::getData('baz'), 'zap');
	}

	public function testGetUrl() {
		$_SERVER['REQUEST_URI'] = '/bar/baz?q=a';
		$this->assertEquals('bar/baz', Request::getUrl());
	}

	public function testGetProtocol() {
		$this->assertEquals(null, Request::getProtocol());

		$_SERVER ['HTTP_HOST'] = 'foo';

		$this->assertEquals('http', Request::getProtocol());

		$_SERVER['HTTPS'] = 'ON';
		$this->assertEquals('https', Request::getProtocol());

	}

}
