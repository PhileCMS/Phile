<?php

namespace Phile\Plugin\Phile\TemplateTwig\Template;

class PagesCollection implements \ArrayAccess, \IteratorAggregate {
    private $pages;
    private $repository;

    public function __construct($repository){
        $this->repository = $repository;
    }

    public function getIterator(){
        $this->load();
        return new \ArrayIterator($this->pages);
    }

    /* Methods */
    public function offsetExists ($offset){
        $this->load();
        return isset($this->pages[$offset]);
    }

    public function offsetGet($offset){
        $this->load();
        return $this->pages[$offset];
    }

    public function offsetSet($offset, $value){
        $this->load();
        $this->pages[$offset] = $value;
    }

    public function offsetUnset($offset){
        $this->load();
        unset($this->pages[$offset]);
    }

    private function load(){
        if ($this->pages === null){
            $this->pages = $this->repository->findAll();
        }
    }
}
