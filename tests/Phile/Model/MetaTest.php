<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:51
 */

namespace PhileTest\Model;

use PHPUnit\Framework\TestCase;

/**
 * the MetaTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class MetaTest extends TestCase
{
    /**
     * @var string meta data test string
     */
    protected $metaTestData1 = "/*
Title: Welcome
Spaced Key: Should become underscored
Nested:
    nested a: 1
    nested B: 2
Description: This description will go in the meta description tag
Date: 2014/08/01
*/
";

    /**
     * @var string meta data test string
     */
    protected $metaTestData2 = "<!--
Title: Welcome
Description: This description will go in the meta description tag
Date: 2014-08-01
-->
";

    /**
     * @var string meta data in YAML front matter format
     */
    protected $metaTestData3 = "---
Title: Welcome
---
";

    /**
     *
     */
    public function testCanGetMetaProperty()
    {
        $meta = new \Phile\Model\Meta($this->metaTestData1);
        $this->assertEquals('Welcome', $meta->get('title'));
        $this->assertEquals(
            'This description will go in the meta description tag',
            $meta->get('description')
        );
        $meta2 = new \Phile\Model\Meta($this->metaTestData2);
        $this->assertEquals('Welcome', $meta2->get('title'));
        $this->assertEquals(
            'This description will go in the meta description tag',
            $meta2->get('description')
        );
    }

    public function testCanGetFormatedDate()
    {
        $meta = new \Phile\Model\Meta($this->metaTestData1);
        $this->assertEquals('1st Aug 2014', $meta->getFormattedDate());
        $meta2 = new \Phile\Model\Meta($this->metaTestData2);
        $this->assertEquals('1st Aug 2014', $meta2->getFormattedDate());
    }

    public function testGetIfNoMetaDataOnPage()
    {
        $meta = new \Phile\Model\Meta("Welcome\n…");
        $this->assertEquals([], $meta->getAll());
        $this->assertNull($meta->get('title'));

        $meta = new \Phile\Model\Meta("/*\n*/\nWelcome\n…");
        $this->assertEquals([], $meta->getAll());
    }

    public function testSpacedKey()
    {
        $meta = new \Phile\Model\Meta($this->metaTestData1);
        $this->assertEquals(
            'Should become underscored',
            $meta->get('spaced_key')
        );
    }

    public function testYamlFrontMatterFormat()
    {
        $meta = new \Phile\Model\Meta($this->metaTestData3);
        $this->assertEquals('Welcome', $meta['title']);
    }
}
