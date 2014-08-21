<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 20.08.14
 * Time: 15:57
 */

namespace PhileTest;


class RegistryTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \Phile\Registry
	 */
	protected $registry;

	protected function setUp() {
		parent::setUp();
		$this->registry = \Phile\Registry::getInstance();
	}

	protected function tearDown() {
		parent::tearDown();
		$this->registry->_unsetInstance();
	}

	public function testValueCanSetToRegistry() {
		$this->registry->set('test', 'testvalue');
		$this->assertEquals('testvalue', $this->registry->get('test'));
	}

	public function testGettingInstance() {
		$this->registry = \Phile\Registry::getInstance();
		$this->assertInstanceOf('\Phile\Registry', $this->registry);
	}

	public function testValueIsRegistered() {
		$this->registry->set('testValueIsRegistered', 'testValueIsRegistered');
		$this->assertEquals(true, $this->registry->isRegistered('testValueIsRegistered'));
	}

	public function testValueIsNotRegistered() {
		$this->assertEquals(false, $this->registry->isRegistered('testValueIsNotRegistered'));
	}


}
 