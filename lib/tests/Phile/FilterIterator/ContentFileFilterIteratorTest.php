<?php

namespace PhileTest\FilterIterator;

use Phile\FilterIterator\ContentFileFilterIterator;
use PHPUnit\Framework\TestCase;

/**
 * class ContentFileIteratorTest
 *
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */
class ContentFileIteratorTest extends TestCase
{
    public function testContentFileFilterIterator()
    {
        $folder = PLUGINS_CORE_DIR . 'phile/testPlugin/content';
        $files = new ContentFileFilterIterator(new \DirectoryIterator($folder));
        $result = [];
        foreach ($files as $file) {
            $result[] = $file->getFilename();
        }
        $this->assertEquals(['a.md'], $result);
    }
}
