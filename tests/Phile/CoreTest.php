<?php

namespace PhileTest;

use Phile\Core;
use Phile\Core\Registry;
use Phile\Core\Response;
use Phile\Core\Router;

/**
 * the CoreTest class
 *
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class CoreTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testInitializeCurrentPageTidyUrlRedirect()
    {
        $baseUrl = 'http://foo';

        $settings = Registry::get('Phile_Settings');
        Registry::set('Phile_Settings', ['base_url' => $baseUrl] + $settings);

        $redirects = [
            'sub' => 'sub/',
            'sub/page/' => 'sub/page'
        ];

        foreach ($redirects as $current => $expected) {
            $Core = $this->getMockBuilder('Phile\Core')
                ->setMethods(
                    [
                        'checkSetup',
                        'initializeErrorHandling',
                        'initializeTemplate'
                    ]
                )
                ->disableOriginalConstructor()
                ->getMock();

            $response = $this->getMockBuilder('\Phile\Core\Response')
                ->setMethods(['redirect', 'stop'])
                ->getMock();
            $router = new Router(['REQUEST_URI' => $current]);

            $response->expects($this->once())
                ->method('redirect')
                ->with($baseUrl . '/' . $expected, 301);

            $Core->__construct($router, $response);
        }
    }

    /**
     * tests show setup page if setup is unfinished
     */
    public function testCheckSetupRedirectToSetupPage()
    {
        $settings = Registry::get('Phile_Settings');
        Registry::set('Phile_Settings', ['encryptionKey' => ''] + $settings);

        $_SERVER['REQUEST_URI'] = '/';

        $response = new Response();
        new Core(new Router, $response);

        $expected = 'Welcome to the PhileCMS Setup';
        $body = $this->getObjectAttribute($response, 'body');
        $this->assertContains($expected, $body);

        // 64 char encryption key on page
        $pattern = '/\<code\>(\s*?).{64}(\s*?)\<\/code\>/';
        $this->assertRegExp($pattern, $body);
    }
}
