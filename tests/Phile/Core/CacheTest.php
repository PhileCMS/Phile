<?php

namespace PhileTest\Core;

use Phile\Core\Cache;
use Phile\Core\ServiceLocator;

/**
 * the CacheTest class
 *
 * @author  PhileCms
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class CacheTest extends \PHPUnit_Framework_TestCase {

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
