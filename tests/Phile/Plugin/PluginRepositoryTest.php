<?php

namespace PhileTest\Plugin;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Plugin\PluginRepository;
use Phile\Test\TestCase;
use Phile\Exception\PluginException;

/**
 * the PluginRepositoryTest class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class PluginRepositoryTest extends TestCase
{

    public function testLoadAllSuccess()
    {
        $toLoad = 'phile\testPlugin';
        $eventBus = new Event;
        $plugins = new PluginRepository($eventBus);
        $plugins->addDirectory(__DIR__ . '/../../fixture/plugins/');
        $config = new Config(['plugins' => [$toLoad => ['active' => true]]]);

        $result = null;
        $eventBus->register('plugins_loaded', function ($name, $data) use (&$result) {
            $result = $data['plugins'];
        });

        $plugins->load($config);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey($toLoad, $result);
        $this->assertInstanceOf(
            '\Phile\Plugin\AbstractPlugin',
            $result[$toLoad]
        );
    }

    public function testLoadAllFailure()
    {
        $plugins = new PluginRepository(new Event);
        $plugins->addDirectory(__DIR__ . '/../../fixture/plugins/');
        $config = new Config(['plugins' => ['foo\bar' => ['active' => true]]]);

        $this->expectException(PluginException::class);
        $plugins->load($config);
    }
}
