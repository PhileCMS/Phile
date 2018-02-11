<?php
/**
 * test plugin used in Phile's unit tests
 */
namespace PhileTest\Plugin;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Plugin\Phile\TestPlugin\Plugin;
use PHPUnit\Framework\TestCase;

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

    protected function setUp()
    {
        $settings['plugins'][$this->pluginKey] = [
            'active' => false,
            'A' => 'A'
        ];
        Registry::set('Phile.Core.Config', new Config($settings));
    }

    protected function mockPlugin(array $methods = [])
    {
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
        $plugin->initializePlugin($this->pluginKey);
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
        $this->assertAttributeSame($expected, 'settings', $plugin);
    }

    public function testInitializePluginEvents()
    {
        $plugin = $this->mockPlugin(['onTestEvent']);
        $plugin->expects($this->once())
            ->method('onTestEvent');
        Event::triggerEvent('phile\testPlugin.testEvent');
    }

    public function testInitializePluginEventsNotCallable()
    {
        $plugin = $this->mockPlugin();
        $this->expectException('\RuntimeException', null, 1428564865);
        Event::triggerEvent('phile\testPlugin.testEvent-missingMethod');
    }

    public function testGetPluginPath()
    {
        $plugin = $this->mockPlugin(['onTextEvent']);

        $result = $plugin->getPluginPath();
        $DS = DIRECTORY_SEPARATOR;
        $expected = PLUGINS_DIR . 'phile' . $DS . 'testPlugin' . $DS;
        $this->assertEquals($expected, $result);

        $result = $plugin->getPluginPath('vendor.php');
        $expected = $expected . 'vendor.php';
        $this->assertEquals($expected, $result);
    }
}
