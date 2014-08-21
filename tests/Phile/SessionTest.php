<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:16
 */

namespace PhileTest;


class SessionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionCanBeStarted() {
		$this->assertEquals(false, \Phile\Session::$isStarted);
		\Phile\Session::start();
		$this->assertEquals(true, \Phile\Session::$isStarted);
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionCanStoreString() {
		\Phile\Session::set('myTestString', 'myTestString');
		$this->assertEquals('myTestString', \Phile\Session::get('myTestString'));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionCanStoreInteger() {
		\Phile\Session::set('myTestInteger', 123);
		$this->assertEquals(123, \Phile\Session::get('myTestInteger'));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionCanStoreBoolean() {
		\Phile\Session::set('myTestBoolean', true);
		$this->assertEquals(true, \Phile\Session::get('myTestBoolean'));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionCanStoreFloat() {
		\Phile\Session::set('myTestFloat', 1.123);
		$this->assertEquals(1.123, \Phile\Session::get('myTestFloat'));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionCanStoreStdClass() {
		\Phile\Session::set('myTestStdClass', new \stdClass());
		$this->assertInstanceOf('\stdClass', \Phile\Session::get('myTestStdClass'));
	}

	/**
	 * @runInSeparateProcess
	 */
	public function testSessionHasSessionId() {
		$this->assertEquals(0, strlen(\Phile\Session::$sessionId));
		\Phile\Session::start();
		$this->assertGreaterThan(0, strlen(\Phile\Session::$sessionId));
	}
}
 