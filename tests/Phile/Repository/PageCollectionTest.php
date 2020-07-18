<?php

namespace PhileTest\Repository;

use Phile\Repository\PageCollection;
use PHPUnit\Framework\TestCase;

/**
 * the PageCollectionTest class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */
class PageCollectionTest extends TestCase
{
    /**
     * @var PageCollection;
     */
    protected $collection;

    protected $fixture = [0 => 'a', 1 => 'b'];

    protected function setUp(): void
    {
        $loader = function () {
            return $this->fixture;
        };
        $this->collection = new PageCollection($loader);
    }

    public function testArrayAccess()
    {
        $result = $this->collection;
        $this->assertEquals($this->fixture[0], $result[0]);
        $this->assertEquals($this->fixture[1], $result[1]);
    }

    public function testCount()
    {
        $result = $this->collection;
        $this->assertEquals(count($this->fixture), count($result));
    }

    public function testTraversable()
    {
        $result = $this->collection;
        foreach ($result as $key => $value) {
            $this->assertEquals($this->fixture[$key], $value);
        }
    }

    public function testToArray()
    {
        $result = $this->collection->toArray();
        $this->assertEquals($this->fixture, $result);
    }
}
