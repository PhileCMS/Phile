<?php
/*
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace PhileTest;

use Phile\Core\Config;
use Phile\Test\TestCase;
use Phile\Core\Container;

class ContainerTest extends TestCase
{
    public function setUp()
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
        $this->expectException(\Phile\Exception\ContainerException::class);
        $this->expectExceptionCode(1398536617);
        $this->container->set('Phile_EventBus', new Config());
    }

    public function testGetItemNotFound()
    {
        $this->expectException(\Phile\Exception\ContainerNotFoundException::class);
        $this->expectExceptionCode(1519111836);
        $this->container->get('The Island');
    }
}
