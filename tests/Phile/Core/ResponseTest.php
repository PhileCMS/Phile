<?php
namespace PhileTest\Core;

use Phile\Core\Response;
use PHPUnit\Framework\TestCase;

/**
 * the ResponseTest class
 *
 * @author  PhileCms
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class ResponseTest extends TestCase
{

    /**
     * @var \Phile\Core\Response
     */
    protected $response;

    protected function setUp()
    {
        parent::setUp();
        $this->response = $this->getMockBuilder('\Phile\Core\Response')
            ->setMethods(['outputHeader', 'stop'])
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->response);
    }

    public function testCreateResponse()
    {
        $response = $this->response->createResponse();
        $expected = \Psr\Http\Message\ResponseInterface::class;
        $this->assertInstanceOf($expected, $response);
    }

    public function testDefaultCharset()
    {
        $this->response = $this->getMockBuilder('\Phile\Core\Response')
            ->setMethods(['setHeader'])
            ->getMock();
        $this->response->expects($this->once())
            ->method('setHeader')
            ->with('Content-Type', 'text/html; charset=utf-8');
        $this->response->send();
    }

    public function testRedirect()
    {
        $location = 'foo';

        $this->response = $this->getMockBuilder('\Phile\Core\Response')
            ->setMethods(['setHeader', 'setStatusCode', 'send', 'stop'])
            ->getMock();

        $this->response->expects($this->once())
            ->method('setStatusCode')
            ->with('301')
            ->will($this->returnSelf());
        $this->response->expects($this->once())
            ->method('setHeader')
            ->with('Location', $location, true)
            ->will($this->returnSelf());
        $this->response->expects($this->once())
            ->method('send')
            ->will($this->returnSelf());
        $this->response->expects($this->once())
            ->method('stop');

        $this->response->redirect($location, 301);

        $this->expectOutputString('');
    }

    public function testSetCharset()
    {
        $this->response = $this->getMockBuilder('\Phile\Core\Response')
            ->setMethods(['setHeader'])
            ->getMock();
        $this->response->expects($this->once())
            ->method('setHeader')
            ->with('Content-Type', 'text/html; charset=latin-1');
        $this->response->setCharset('latin-1')->send();
    }

    public function testSetBody()
    {
        $this->response->setBody('foo')->send();
        $this->expectOutputString('foo');
    }
}
