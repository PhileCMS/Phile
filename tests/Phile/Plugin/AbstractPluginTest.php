<?php
/**
 * test plugin used in Phile's unit tests
 */
namespace PhileTest\Plugin;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Container;
use Phile\Plugin\Phile\TestPlugin\Plugin;
use Phile\Test\TestCase;
use Phile\Plugin\PluginDirectory;

/**
 * the AbstractPluginTest class
 *
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class AbstractPluginTest extends TestCase
{

    protected $pluginKey = 'phile\testPlugin';

    protected $pluginDir;

    /**
     * @var Event The event-bus.
     */
    protected $eventBus;

    protected function mockPlugin(array $methods = [])
    {
        $this->pluginDir = realpath(__DIR__ . '/../../fixture/plugins/') . '/';
        $directory = new PluginDirectory($this->pluginDir);

        /** @var Plugin $plugin */
        $plugin = $this->getMockForAbstractClass(
            '\Phile\Plugin\Phile\TestPlugin\Plugin',
            [],
            '',
            true,
            true,
            true,
            $methods
        );

        $this->eventBus = new Event;

        $config['plugins'][$this->pluginKey] = [
            'active' => false,
            'A' => 'A'
        ];

        $plugin->initializePlugin(
            $this->pluginKey,
            $directory->getPath(),
            $this->eventBus,
            new Config($config)
        );
        return $plugin;
    }

    public function testInitializePluginSettings()
    {
        $plugin = $this->mockPlugin();

        // class:    ['A' => 'X', 'B' => 'X', 'C' => 'C'];
        // defaults: ['A' => 'X', 'B' => 'B'];
        // global:   ['A' => 'A', 'active' => false];
        $expected = [
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'active' => false
        ];

        $this->assertObjectHasAttribute('settings', $plugin);
        $this->assertEquals($expected, $plugin->getSettings());
    }

    public function testInitializePluginEvents()
    {
        $plugin = $this->mockPlugin(['onTestEvent']);
        $plugin->expects($this->once())
            ->method('onTestEvent');
        $this->eventBus->trigger('phile\testPlugin.testEvent');
    }

    public function testInitializePluginEventsNotCallable()
    {
        $plugin = $this->mockPlugin();
        $this->expectException('\RuntimeException', null, 1428564865);
        $this->eventBus->trigger('phile\testPlugin.testEvent-missingMethod');
    }

    public function testGetPluginPath()
    {
        $plugin = $this->mockPlugin(['onTextEvent']);

        $result = $plugin->getPluginPath();
        $DS = DIRECTORY_SEPARATOR;
        $expected = $this->pluginDir . 'phile' . $DS . 'testPlugin' . $DS;
        $this->assertEquals($expected, $result);

        $result = $plugin->getPluginPath('vendor.php');
        $expected = $expected . 'vendor.php';
        $this->assertEquals($expected, $result);
    }
}
