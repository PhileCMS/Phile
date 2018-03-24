<?php
/**
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace PhileTest;

use Phile\Core\Config;
use Phile\Core\Container;
use Phile\Test\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * the CoreTest class
 */
class PhileTest extends TestCase
{
    public function testPageNotFound()
    {
        $core = $this->createPhileCore();
        $request = $this->createServerRequestFromArray(['REQUEST_URI' => '/abcd']);
        $response = $this->createPhileResponse($core, $request);

        $this->assertEquals($response->getStatusCode(), '404');
        $this->assertContains(
            'Woops. Looks like this page doesn\'t exist.',
            (string)$response->getBody()
        );
    }

    public function testEarlyResponse()
    {
        $events = [
            'after_init_core',
            'request_uri',
            'after_resolve_page',
            'before_render_template',
            'after_response_created'
        ];
        foreach ($events as $event) {
            $core = $this->createPhileCore();
            $eventBus = Container::getInstance()->get('Phile_EventBus');
            $expected = (new \Phile\Core\Response)->createHtmlResponse($event);
            $eventBus->register($event, function ($name, $data) use ($expected) {
                $data['response'] = $expected;
            });

            $request = $this->createServerRequestFromArray(['REQUEST_URI' => '/abcd']);
            $actual = $this->createPhileResponse($core, $request);
            $this->assertSame($expected, $actual);
        }
    }
    
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
        $response = $this->createPhileResponse($core, $request);

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
            $response = $this->createPhileResponse($core, $request);
            $this->assertSame(301, $response->getStatusCode());
            $this->assertSame($baseUrl . '/' . $expected, $response->getHeader('Location')[0]);
        }
    }

    public function testDontHandleNotFound()
    {
        $config = new Config(['handle_not_found' => false]);
        $core = $this->createPhileCore(null, $config);

        $core->addMiddleware(function ($queue) {
            $middleware = new class implements MiddlewareInterface
            {
                public function process(
                    ServerRequestInterface $request,
                    RequestHandlerInterface $handler
                ): ResponseInterface {
                    return (new \Phile\Core\Response)->createHtmlResponse('foobar');
                }
            };
            $queue->add($middleware, 0);
        });

        $request = $this->createServerRequestFromArray(['REQUEST_URI' => '/baz']);
        $response = $this->createPhileResponse($core, $request);
        $this->assertEquals('foobar', (string)$response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
