<?php
/**
 * test plugin used in Phile's unit tests
 */
namespace PhileTest\Plugin;

use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Plugin\Phile\TestPlugin\Plugin;

/**
 * the AbstractPluginTest class
 *
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class AbstractPluginTest extends \PHPUnit_Framework_TestCase {

	protected $pluginKey = 'phile\testPlugin';

	protected function setUp() {
		$settings = Registry::get('Phile_Settings');
		$settings['plugins'][$this->pluginKey] =  [
			'active' => false,
			'A' => 'A'
		];
		Registry::set('Phile_Settings', $settings);
	}

	protected function tearDown() {
		$settings = Registry::get('Phile_Settings');
		unset($settings['plugins'][$this->pluginKey]);
		Registry::set('Phile_Settings', $settings);
	}


	public function testInitializePluginSettings() {
		$plugin = new Plugin();

		// class:    ['A' => 'X', 'B' => 'X', 'C' => 'C'];
		// defaults: ['A' => 'X', 'B' => 'B'];
		// global:   ['A' => 'A', 'active' => false];
		$expected = [
			'A' => 'A',
			'B' => 'B',
			'C' => 'C',
			'active' => false
		];
		$plugin->initializePlugin($this->pluginKey);
		$this->assertAttributeSame($expected, 'settings', $plugin);
	}

	public function testInitializePluginEvents() {
		$plugin = $this->getMock(
			'\Phile\Plugin\Phile\TestPlugin\Plugin',
			['onTestEvent']
		);
		$plugin->initializePlugin($this->pluginKey);
		$plugin->expects($this->once())
			->method('onTestEvent');
		Event::triggerEvent('phile\testPlugin.testEvent');
	}

	public function testGetPluginPath() {
		$plugin = new Plugin();
		$plugin->initializePlugin($this->pluginKey);

		$result = $plugin->getPluginPath();
		$DS = DIRECTORY_SEPARATOR;
		$expected = PLUGINS_DIR . 'phile' . $DS . 'testPlugin' . $DS;
		$this->assertEquals($expected, $result);

		$result = $plugin->getPluginPath('vendor.php');
		$expected = $expected . 'vendor.php';
		$this->assertEquals($expected, $result);
	}

}
