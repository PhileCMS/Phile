<?php

namespace Phile\Plugin\Phile\PhpFastCache\Tests;

use Phile\Plugin\Phile\PhpFastCache\PhileToPsr16CacheAdapter;
use Phpfastcache\Helper\Psr16Adapter;
use Phile\Test\TestCase;

/**
 * Tests for PhileToPsr16CacheAdapter class
 *
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\PhpFastCache\Tests
 */
class PhileToPsr16CacheAdapterTest extends TestCase
{
    public function testSlug()
    {
        $this->createPhileCore()->bootstrap();

        $psr16Cache = new Psr16Adapter('memstatic');
        $adapter = new PhileToPsr16CacheAdapter($psr16Cache);

        $adapter->set("{}()\/:@", 'foo');
        $this->assertSame('foo', $adapter->get("{}()\/:@"));

        $adapter->set('}foo', 'bar');
        $adapter->set('{foo', 'baz');
        $this->assertSame('bar', $adapter->get('}foo'));
    }
}
