<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:16
 */

namespace PhileTest;


/**
 * the SessionTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class SessionTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSessionCanBeStarted() {
		$this->assertEquals(false, \Phile\Session::$isStarted);
		\Phile\Session::start();
		$this->assertEquals(true, \Phile\Session::$isStarted);
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSessionCanStoreString() {
		\Phile\Session::set('myTestString', 'myTestString');
		$this->assertEquals('myTestString', \Phile\Session::get('myTestString'));
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSessionCanStoreInteger() {
		\Phile\Session::set('myTestInteger', 123);
		$this->assertEquals(123, \Phile\Session::get('myTestInteger'));
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSessionCanStoreBoolean() {
		\Phile\Session::set('myTestBoolean', true);
		$this->assertEquals(true, \Phile\Session::get('myTestBoolean'));
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSessionCanStoreFloat() {
		\Phile\Session::set('myTestFloat', 1.123);
		$this->assertEquals(1.123, \Phile\Session::get('myTestFloat'));
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSessionCanStoreStdClass() {
		\Phile\Session::set('myTestStdClass', new \stdClass());
		$this->assertInstanceOf('\stdClass', \Phile\Session::get('myTestStdClass'));
	}

	/**
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function testSessionHasSessionId() {
		$this->assertEquals(0, strlen(\Phile\Session::$sessionId));
		\Phile\Session::start();
		$this->assertGreaterThan(0, strlen(\Phile\Session::$sessionId));
	}
}
 