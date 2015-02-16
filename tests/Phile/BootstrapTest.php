<?php

namespace PhileTest;

use Phile\Bootstrap;

/**
 * the BootstrapTest class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class BootstrapTest extends \PHPUnit_Framework_TestCase {

  /**
   * test creation of files and folders
   */
  public function testInitializeFilesAndFolders() {
    $paths = [CACHE_DIR, STORAGE_DIR];

    //setup: delete files and folders
    foreach ($paths as $path) {
      $this->deleteDirectory($path);
      $this->assertFalse(is_dir($path));
    }

    Bootstrap::getInstance()->initializeBasics();

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
  protected function deleteDirectory($path) {
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
