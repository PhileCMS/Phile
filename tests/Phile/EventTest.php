<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 18:24
 */

namespace PhileTest;


class EventTest extends \PHPUnit_Framework_TestCase {

	public function testEventCanBeRegistered() {
		$eventObserverClass = $this->getMock('\Phile\Gateway\EventObserverInterface');
		\Phile\Event::registerEvent('myTestEvent', $eventObserverClass);
		$this->assertArrayHasKey('myTestEvent', \PHPUnit_Framework_Assert::readAttribute(\Phile\Event, '_registry'));
	}
}
 