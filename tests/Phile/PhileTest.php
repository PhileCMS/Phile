<?php
/*
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace PhileTest;

use Phile\Phile;
use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Router;
use Phile\Test\TestCase;

/**
 * the CoreTest class
 */
class PhileTest extends TestCase
{
    public function testConfigIsInitialized()
    {
        new Phile;
        $this->assertInstanceOf(Config::class, Registry::get('Phile.Core.Config'));

        $config = new Config;
        new Phile(null, $config);
        $this->assertSame($config, Registry::get('Phile.Core.Config'));
    }

    public function testEventBusIsInitialized()
    {
        new Phile;
        $this->assertInstanceOf(Event::class, Registry::get('Phile.Core.EventBus'));

        $event = new Event;
        new Phile($event);
        $this->assertSame($event, Registry::get('Phile.Core.EventBus'));

        //= test deprecated
        $this->assertSame($event, Event::getInstance());
    }

    /**
     * tests show setup page if setup is unfinished
     */
    public function testCheckSetupRedirectToSetupPage()
    {
        $_SERVER['REQUEST_URI'] = '/';

        $event = new Event;
        $event->register(
            'config_loaded',
            function ($event, $data) {
                $data['class']->set('encryptionKey', '');
            }
        );

        $core = $this->getBootstrappedCore($event);
        $response = $this->dispatchCore($core);

        $expected = 'Welcome to the PhileCMS Setup';
        $body = (string)$response->getBody();
        $this->assertContains($expected, $body);

        // 64 char encryption key on page
        $pattern = '/\<code\>(\s*?).{64}(\s*?)\<\/code\>/';
        $this->assertRegExp($pattern, $body);
    }

    public function testInitializeCurrentPageTidyUrlRedirect()
    {
        $baseUrl = 'http://foo';

        $redirects = [
            'sub' => 'sub/',
            'sub/page/' => 'sub/page'
        ];

        foreach ($redirects as $current => $expected) {
            $config = new Config(['base_url' => $baseUrl]);
            $_SERVER = ['REQUEST_URI' => $current] + $_SERVER;
            $core = $this->getBootstrappedCore(null, $config);
            $response = $this->dispatchCore($core);
            $this->assertSame(301, $response->getStatusCode());
            $this->assertSame($baseUrl . '/' . $expected, $response->getHeader('Location')[0]);
        }
    }
}
