<?php

namespace PhileTest\Core;

use Phile\Core\Utility;
use PHPUnit\Framework\TestCase;

/**
 * Utility test class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class UtilityTest extends TestCase
{

    public function testGetFiles()
    {
        $folder = PLUGINS_DIR . 'phile/testPlugin/content/';

        $sub = $folder . 'sub/';
        $symlink = $folder . 'symlink';
        if (file_exists($symlink)) {
            unlink($symlink);
        };
        if (!symlink($sub, $symlink)) {
            $this->markTestSkipped("Couldn't create symlink $symlink to $sub.");
        }

        $expected = ['a.md', 'sub/c.md', 'symlink/c.md'];
        foreach ($expected as $key => $file) {
            $expected[$key] = $folder . $file;
        }
        sort($expected);

        $files = Utility::getFiles(
            $folder,
            '\Phile\FilterIterator\ContentFileFilterIterator'
        );
        sort($files);

        $this->assertEquals($expected, $files);

        unlink($symlink);
    }
}
