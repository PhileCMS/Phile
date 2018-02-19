<?php

namespace Phile\Plugin\Phile\PhpFastCache\Tests;

use Phile\Plugin\Phile\PhpFastCache\PhileToPsr16CacheAdapter;
use phpFastCache\Helper\Psr16Adapter;
use Phile\Test\TestCase;

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
