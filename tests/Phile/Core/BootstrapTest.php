<?php

namespace PhileTest;

use Phile\Phile as Core;
use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Test\TestCase;

/**
 * the CoreTest class
 *
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class Bootstrap extends TestCase
{
    /**
     * test creation of files and folders
     */
    public function testInitializeFilesAndFolders()
    {
        $config = new Config;
        $this->getBootstrappedCore(null, $config);

        $paths = [$config->get('cache_dir'), $config->get('storage_dir')];

        //setup: delete files and folders
        foreach ($paths as $path) {
            if (empty($path) || strpos($path, $config->get('root_dir')) !== 0) {
                $this->markTestSkipped("Path $path is not in Phile installation directory.");
            }
            $this->deleteDirectory($path);
            $this->assertFalse(is_dir($path));

            \Phile\Core\Bootstrap::setupFolder($path, $config);
        }

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
