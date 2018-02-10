<?php

namespace PhileTest;

use Phile\Core;
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
class CoreTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        // reset settings, events, plugins, … before before each test
        (new Core())->initialize();
    }

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
                    ['setErrorHandler', 'renderHtml']
                )
                ->getMock();
            $Core->method('setErrorHandler')->will($this->returnSelf());
            $Core->method('renderHtml')->will($this->returnSelf());

            $response = $this->getMockBuilder('\Phile\Core\Response')
                ->setMethods(['redirect', 'stop'])
                ->getMock();
            $router = new Router(['REQUEST_URI' => $current]);

            $response->expects($this->once())
                ->method('redirect')
                ->with($baseUrl . '/' . $expected, 301);

            $Core->initialize()->dispatch($router, $response);
        }
    }

    /**
     * tests show setup page if setup is unfinished
     */
    public function testCheckSetupRedirectToSetupPage()
    {
        $router = new Router(['REQUEST_URI' => '/']);
        $response = new Response();

        $core = (new Core())->initialize();

        $settings = Registry::get('Phile_Settings');
        Event::triggerEvent('config_loaded', ['config' => ['encryptionKey' => ''] + $settings]);
        Event::triggerEvent('setup_check');

        $core->dispatch($router, $response);

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

        (new Core)->initialize();

        foreach ($paths as $path) {
            $this->assertTrue(is_dir($path));
            $this->assertTrue(is_file($path . '.htaccess'));
        }
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
