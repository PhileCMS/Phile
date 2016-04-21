<?php

namespace PhileTest\FilterIterator;

use Phile\FilterIterator\ContentFileFilterIterator;

/**
 * class ContentFileIteratorTest
 *
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */
class ContentFileIteratorTest extends \PHPUnit_Framework_TestCase
{

    public function testContentFileFilterIterator()
    {
        $folder = PLUGINS_DIR . 'phile/testPlugin/content';
        $files = new ContentFileFilterIterator(new \DirectoryIterator($folder));
        $result = [];
        foreach ($files as $file) {
            $result[] = $file->getFilename();
        }
        $this->assertEquals(['a.md'], $result);
    }
}
