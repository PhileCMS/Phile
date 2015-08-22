<?php

namespace PhileTest\Core;

use Phile\Core\Registry;
use Phile\Core\Router;

/**
 * the Router class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    protected $settings;

    /**
     * @var Router
     */
    protected $router;

    protected function setUp()
    {
        $this->router = new Router();
        $this->settings = Registry::get('Phile_Settings');
        parent::setup();
    }

    protected function tearDown()
    {
        Registry::set('Phile_Settings', $this->settings);
        unset($this->router, $this->settings);
    }

    public function testUrlForPageFull()
    {
        $this->mockBaseUrl('http://barbaz');
        $page = 'index/foo';
        $expected = 'http://barbaz/index/foo';
        $result = $this->router->urlForPage($page);
        $this->assertEquals($expected, $result);
    }

    public function testUrlForPageRelative()
    {
        $page = 'index/foo';
        $expected = 'index/foo';
        $result = $this->router->urlForPage($page, false);
        $this->assertEquals($expected, $result);
    }

    public function testUrl()
    {
        $this->mockBaseUrl('http://barbaz');
        $expected = 'http://barbaz/sub/page';
        $result = $this->router->url('sub/page');
        $this->assertEquals($expected, $result);
    }

    public function testGetBaseUrl()
    {
        $this->mockBaseUrl();

        $server = ['PHP_SELF' => '/bar/index.php', 'HTTP_HOST' => 'foo'];
        $router = new Router($server);
        $this->assertEquals('http://foo/bar', $router->getBaseUrl());
    }

    /**
     * test baseUrl on `php -S localhost` server
     */
    public function testGetBaseUrlPhpBuildInServer()
    {
        $this->mockBaseUrl();

        $server = ['PHP_SELF' => '/foo/index.php', 'HTTP_HOST' => 'host'];
        $router = new Router($server);
        $this->assertEquals('http://host/foo', $router->getBaseUrl());

        $server = [
            'PHP_SELF' => '/foo/index.php/sub/page',
            'HTTP_HOST' => 'host'
        ];
        $router = new Router($server);
        $this->assertEquals('http://host/foo', $router->getBaseUrl());
    }

    public function testGetBaseUrlPreset()
    {
        $this->mockBaseUrl('https://barbaz');
        $this->assertEquals('https://barbaz', $this->router->getBaseUrl());
    }

    /**
     * test that URL is UTF8-decoded
     */
    public function testGetUrlUrlDecoded()
    {
        $router = new Router(['REQUEST_URI' => '/bar/b%C3%A4z%20page?q=a']);
        $this->assertEquals('bar/bäz page', $router->getCurrentUrl());
    }

    /**
     * test that base-URL is removed
     */
    public function testGetUrlRemoveUrlPath()
    {
        $pathFragment = 'sub';
        // assume installation in http://localhost/sub
        $this->mockBaseUrl('http://localhost/' . $pathFragment);

        // incoming request-URI: /sub/sub/page
        $requestUri = '/' . $pathFragment . '/' . $pathFragment . '/page';
        $router = new Router(['REQUEST_URI' => $requestUri]);
        // requested page: sub/page.md
        $this->assertEquals('sub/page', $router->getCurrentUrl());
    }

    public function testGetProtocol()
    {
        $this->assertEquals(null, $this->router->getProtocol());

        $router = new Router(['HTTP_HOST' => 'foo']);
        $this->assertEquals('http', $router->getProtocol());

        $router = new Router(['HTTP_HOST' => 'foo', 'HTTPS' => 'ON']);
        $this->assertEquals('https', $router->getProtocol());
    }

    public function mockBaseUrl($url = '')
    {
        Registry::set(
            'Phile_Settings',
            ['base_url' => $url] + $this->settings
        );
    }
}
