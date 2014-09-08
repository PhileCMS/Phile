<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 18:24
 */

namespace PhileTest;


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

		\Phile\Core\Event::registerEvent('myTestEvent', $mock);
		\Phile\Core\Event::triggerEvent('myTestEvent');
	}
}
 