<?php
/*
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace PhileTest;

use Phile\Core\Config;
use Phile\Test\TestCase;

/**
 * the CoreTest class
 */
class PhileTest extends TestCase
{
    /**
     * tests show setup page if setup is unfinished
     */
    public function testCheckSetupRedirectToSetupPage()
    {
        $config = new Config(['encryptionKey' => '']);
        $core = $this->createPhileCore(null, $config);

        $request = $this->createServerRequestFromArray(
            ['REQUEST_URI' => '/'] + $_SERVER
        );
        $response = $core->dispatch($request);

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
            $core = $this->createPhileCore(null, $config);
            $request = $this->createServerRequestFromArray(
                ['REQUEST_URI' => $current] + $_SERVER
            );
            $response = $core->dispatch($request);
            $this->assertSame(301, $response->getStatusCode());
            $this->assertSame($baseUrl . '/' . $expected, $response->getHeader('Location')[0]);
        }
    }
}
