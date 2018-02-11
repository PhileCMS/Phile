<?php

namespace PhileTest;

use Phile\Phile as Core;
use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Response;
use Phile\Core\Router;
use PHPUnit\Framework\TestCase;

/**
 * the CoreTest class
 *
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class PhileTest extends TestCase
{
    /**
     *
     */
    public function testInitializeCurrentPageTidyUrlRedirect()
    {
        $baseUrl = 'http://foo';

        $redirects = [
            'sub' => 'sub/',
            'sub/page/' => 'sub/page'
        ];


        foreach ($redirects as $current => $expected) {
            $Config = new Config(['base_url' => $baseUrl]);
            $Core = $this->getMockBuilder('Phile\Phile')
                ->setConstructorArgs([null, $Config])
                ->setMethods(['renderHtml'])
                ->getMock();
            $Core->method('renderHtml')->will($this->returnSelf());

            $response = $this->getMockBuilder('\Phile\Core\Response')
                ->setMethods(['redirect'])
                ->getMock();
            $router = new Router(['REQUEST_URI' => $current]);

            $response->expects($this->once())
                ->method('redirect')
                ->with($baseUrl . '/' . $expected, 301);

            $Core->dispatch($router, $response);
        }
    }

    /**
     * tests show setup page if setup is unfinished
     */
    public function testCheckSetupRedirectToSetupPage()
    {
        $router = new Router(['REQUEST_URI' => '/']);
        $response = new Response();

        $event = new Event;
        $event->register(
            'config_loaded',
            function ($event, $data) {
                $data['class']->set('encryptionKey', '');
            }
        );
        $core = (new Core($event));

        $response = $core->dispatch($router, $response);

        $expected = 'Welcome to the PhileCMS Setup';
        $body = $this->getObjectAttribute($response, 'body');
        $this->assertContains($expected, $body);

        // 64 char encryption key on page
        $pattern = '/\<code\>(\s*?).{64}(\s*?)\<\/code\>/';
        $this->assertRegExp($pattern, $body);
    }
    
    /**
     * test creation of files and folders
     */
    public function testInitializeFilesAndFolders()
    {
        $paths = [CACHE_DIR, STORAGE_DIR];

        //setup: delete files and folders
        foreach ($paths as $path) {
            if (empty($path) || strpos($path, ROOT_DIR) !== 0) {
                $this->markTestSkipped("Path $path is not in Phile installation directory.");
            }
            $this->deleteDirectory($path);
            $this->assertFalse(is_dir($path));
        }

        (new Core);

        foreach ($paths as $path) {
            $this->assertTrue(is_dir($path));
            $this->assertTrue(is_file($path . '.htaccess'));
        }
    }

    public function testGetConfig()
    {
        $config = new Config;
        (new Core(null, $config));
        $this->assertSame($config, Registry::get('Phile.Core.Config'));
    }

    public function testGetEventBus()
    {
        $event = new Event;
        (new Core($event));
        $this->assertSame($event, Registry::get('Phile.Core.EventBus'));
    }

    /**
     * deletes $path recursively
     *
     * @param string $path
     */
    protected function deleteDirectory($path)
    {
        if (!is_dir($path)) {
            return;
        }

        $files = glob($path . '*', GLOB_MARK);

        // find .htaccess
        $invisibleFiles =  glob($path . '.*');
        foreach ($invisibleFiles as $key => $file) {
            $basename = basename($file);
            if ($basename === '..' || $basename === '.') {
                unset($invisibleFiles[$key]);
            }
        }
        $files = array_merge($files, $invisibleFiles);

        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($path);
    }
}
