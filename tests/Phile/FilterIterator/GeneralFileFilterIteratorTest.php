<?php

namespace PhileTest\FilterIterator;

use Phile\FilterIterator\GeneralFileFilterIterator;

/**
 * class GeneralFileFilterIteratorTest
 *
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */
class GeneralFileFilterIteratorTest extends \PHPUnit_Framework_TestCase
{

    public function testGeneralFileFilterIterator()
    {
        $folder = PLUGINS_DIR . 'phile/testPlugin/content';
        $files = new GeneralFileFilterIterator(new \DirectoryIterator($folder));
        $result = [];
        foreach ($files as $file) {
            $result[] = $file->getFilename();
        }
        sort($result);
        $this->assertEquals(['.', '..', 'a.md', 'b.txt', 'sub'], $result);
    }
}
