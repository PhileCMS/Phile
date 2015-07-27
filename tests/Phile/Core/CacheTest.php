<?php

namespace PhileTest\Core;

use Phile\Core\Cache;
use Phile\Core\ServiceLocator;
use Phile\Test\PhileTestCase;

/**
 * the CacheTest class
 *
 * @author  PhileCms
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class CacheTest extends PhileTestCase {

	public function testBasics() {
		$this->assertNull(Cache::get('foo'));

		Cache::set('foo', 'bar');
		$this->assertEquals('bar', Cache::get('foo'));

		Cache::set('baz', 'zap');
		Cache::delete('foo');
		$this->assertNull(Cache::get('foo'));
		$this->assertNotNull(Cache::get('baz'));

		Cache::clean();
		$this->assertNull(Cache::get('foo'));
	}

	public function testBasicsNoCache() {
		ServiceLocator::remove('Phile_Cache');
		Cache::set('foo', 'bar');
		$this->assertNull(Cache::get('foo'));
		Cache::delete('baz');
		Cache::clean();
	}

	public function testRemember() {
		$cache = ServiceLocator::getService('Phile_Cache');

		// setup
		$key = 'CacheTest.foo';
		$this->assertFalse($cache->has($key));

		// set key for first time
		$result = Cache::remember($key, function() { return 'bar'; });
		$this->assertEquals('bar', $result);
		$this->assertTrue($cache->has($key));
		$this->assertEquals('bar', $cache->get($key));

		// try to set key second time
		$cache->set($key, 'baz');
		$result = Cache::remember($key, function() { return 'zap'; });
		$this->assertEquals('baz', $result);
		$this->assertEquals('baz', $cache->get($key));
	}
}
