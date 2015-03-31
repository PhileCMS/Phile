<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 18:24
 */

namespace PhileTest;

use Phile\Core\Event;


/**
 * the EventTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class EventTest extends \PHPUnit_Framework_TestCase {

	/**
	 *
	 */
	public function testEventCanBeRegistered() {
		$mock = $this->getMock('Phile\Gateway\EventObserverInterface', array('on'));
		$mock->expects($this->once())
			->method('on');

		Event::registerEvent('myTestEvent', $mock);
		Event::triggerEvent('myTestEvent');
	}

	public function testRegisterAndTriggerCallback() {
		$mock = $this->getMock('stdClass', ['foo']);
		$mock->expects($this->exactly(2))->method('foo');

		Event::registerEvent('myTestEvent', [$mock, 'foo']);
		Event::triggerEvent('myTestEvent');

		$callable = function () use ($mock) {
			$mock->foo();
		};
		Event::registerEvent('myTestEvent2', $callable);
		Event::triggerEvent('myTestEvent2');
	}

	public function testRegisterFail() {
		$this->setExpectedException('\InvalidArgumentException');
		Event::registerEvent('myTestEvent2', new \stdClass());
	}

}
