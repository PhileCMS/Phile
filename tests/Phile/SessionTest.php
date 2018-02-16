<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:16
 */

namespace PhileTest;

use PHPUnit\Framework\TestCase;

/**
 * the SessionTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class SessionTest extends TestCase
{
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionCanBeStarted()
    {
        $this->assertEquals(false, \Phile\Core\Session::$isStarted);
        \Phile\Core\Session::start();
        $this->assertEquals(true, \Phile\Core\Session::$isStarted);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionCanStoreString()
    {
        \Phile\Core\Session::set('myTestString', 'myTestString');
        $this->assertEquals(
            'myTestString',
            \Phile\Core\Session::get('myTestString')
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionCanStoreInteger()
    {
        \Phile\Core\Session::set('myTestInteger', 123);
        $this->assertEquals(123, \Phile\Core\Session::get('myTestInteger'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionCanStoreBoolean()
    {
        \Phile\Core\Session::set('myTestBoolean', true);
        $this->assertEquals(true, \Phile\Core\Session::get('myTestBoolean'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionCanStoreFloat()
    {
        \Phile\Core\Session::set('myTestFloat', 1.123);
        $this->assertEquals(1.123, \Phile\Core\Session::get('myTestFloat'));
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionCanStoreStdClass()
    {
        \Phile\Core\Session::set('myTestStdClass', new \stdClass());
        $this->assertInstanceOf(
            '\stdClass',
            \Phile\Core\Session::get('myTestStdClass')
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testSessionHasSessionId()
    {
        $this->assertEquals(0, strlen(\Phile\Core\Session::$sessionId));
        \Phile\Core\Session::start();
        $this->assertGreaterThan(0, strlen(\Phile\Core\Session::$sessionId));
    }
}
