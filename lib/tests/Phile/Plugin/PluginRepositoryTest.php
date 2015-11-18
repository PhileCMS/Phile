<?php

namespace PhileTest\Plugin;

use Phile\Plugin\PluginRepository;

/**
 * the PluginRepositoryTest class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class PluginRepositoryTest extends \PHPUnit_Framework_TestCase
{

    public function testLoadAllSuccess()
    {
        $toLoad = 'phile\testPlugin';
        $plugins = new PluginRepository(PLUGINS_CORE_DIR);

        $result = $plugins->loadAll([$toLoad => ['active' => true]]);
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey($toLoad, $result);
        $this->assertInstanceOf(
            '\Phile\Plugin\AbstractPlugin',
            $result[$toLoad]
        );

        $this->assertEquals(0, count($plugins->getLoadErrors()));
    }

    public function testLoadAllFailure()
    {
        $plugins = new PluginRepository(PLUGINS_CORE_DIR);

        $plugins->loadAll(['foo\\bar' => ['active' => false]]);
        $this->assertEquals(0, count($plugins->getLoadErrors()));

        $plugins->loadAll(['foo\\bar' => ['active' => true]]);
        $this->assertEquals(1, count($plugins->getLoadErrors()));
    }
}
