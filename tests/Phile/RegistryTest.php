<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 20.08.14
 * Time: 15:57
 */

namespace PhileTest;


/**
 * the RegistryTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class RegistryTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \Phile\Registry
	 */
	protected $registry;

	/**
	 *
	 */
	protected function setUp() {
		parent::setUp();
		$this->registry = \Phile\Core\Registry::getInstance();
	}

	/**
	 *
	 */
	public function testValueCanSetToRegistry() {
		$this->registry->set('test', 'testvalue');
		$this->assertEquals('testvalue', $this->registry->get('test'));
	}

	/**
	 *
	 */
	public function testGettingInstance() {
		$this->registry = \Phile\Core\Registry::getInstance();
		$this->assertInstanceOf('\Phile\Core\Registry', $this->registry);
	}

	/**
	 *
	 */
	public function testValueIsRegistered() {
		$this->registry->set('testValueIsRegistered', 'testValueIsRegistered');
		$this->assertEquals(true, $this->registry->isRegistered('testValueIsRegistered'));
	}

	/**
	 *
	 */
	public function testValueIsNotRegistered() {
		$this->assertEquals(false, $this->registry->isRegistered('testValueIsNotRegistered'));
	}
}
 