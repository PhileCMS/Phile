<?php

namespace PhileTest\Core;

use Phile\Core\Response;

/**
* the ResponseTest class
*
* @author  PhileCms
* @link    https://philecms.com
* @license http://opensource.org/licenses/MIT
* @package PhileTest
*/
class ResponseTest extends \PHPUnit_Framework_TestCase {

  /**
   * @var \Phile\Core\Response
   */
  protected $response;

  protected function setUp() {
    parent::setUp();
    $this->response = $this->getMock('\Phile\Core\Response', ['setHeader']);
  }

  protected function tearDown() {
    unset($this->response);
  }

  public function testDefaultCharset() {
    $this->response->expects($this->once())
      ->method('setHeader')
      ->with('Content-Type', 'text/html; charset=utf-8');
    $this->response->send();
  }

  public function testSetCharset() {
    $this->response->expects($this->once())
      ->method('setHeader')
      ->with('Content-Type', 'text/html; charset=latin-1');
    $this->response->setCharset('latin-1')->send();
  }

  public function testSetBody() {
    $this->response->setBody('foo');
    $this->response->send();
    $this->expectOutputString('foo');
  }

}