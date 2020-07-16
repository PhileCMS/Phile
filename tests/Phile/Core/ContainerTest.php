<?php
/**
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */

namespace PhileTest\Core;

use Phile\Core\Config;
use Phile\Core\Container;
use Phile\Exception\ContainerException;
use Phile\Exception\ContainerNotFoundException;
use Phile\Test\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContainerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $config = [
            'types' => [
                'Phile_EventBus' => Phile\Core\Event::class
            ]
        ];
        $this->container = new Container($config);
    }

    public function testSetGenericItem()
    {
        $obj = new \stdClass;
        $this->container->set('foo', $obj);
        $result = $this->container->get('foo');
        $this->assertSame($obj, $result);
    }

    public function testTypeCheckFailing()
    {
        $this->assertTrue(is_subclass_of(ContainerException::class, ContainerExceptionInterface::class));
        $this->expectException(ContainerException::class);
        $this->expectExceptionCode(1398536617);
        $this->container->set('Phile_EventBus', new Config());
    }

    public function testGetItemNotFound()
    {
        $this->assertTrue(is_subclass_of(ContainerNotFoundException::class, NotFoundExceptionInterface::class));
        $this->expectException(ContainerNotFoundException::class);
        $this->expectExceptionCode(1519111836);
        $this->container->get('The Island');
    }
}
