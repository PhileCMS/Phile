<?php

namespace Phile\Repository;

/**
 * Page collection which delays searching for and loading pages until necessary.
 *
 * @author  PhileCMS
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Repository
 */
class PageCollection implements \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * @var callable A function to be used for loading the pages.
     */
    private $loader;

    /**
     * @var array of \Phile\Model\Page
     */
    private $pages;

    /**
     * Constructor.
     *
     * @param callable $loader pages loader
     */
    public function __construct(callable $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Perform page loading.
     *
     * @return void
     */
    private function load()
    {
        if ($this->pages === null) {
            $this->pages = call_user_func($this->loader);
        }
    }

    /**
     * Get pages in a array.
     *
     * @return array of \Phile\Model\Page
     */
    public function toArray()
    {
        $this->load();
        return $this->pages;
    }

    public function getIterator(): \Traversable
    {
        $this->load();
        return new \ArrayIterator($this->pages);
    }

    public function offsetExists($offset): bool
    {
        $this->load();
        return isset($this->pages[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        $this->load();
        return $this->pages[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->load();
        $this->pages[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        $this->load();
        unset($this->pages[$offset]);
    }

    public function count(): int
    {
        $this->load();
        return count($this->pages);
    }
}
