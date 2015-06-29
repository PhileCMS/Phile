<?php

namespace Phile\Repository;

use Phile\Core\Utility;

/**
 * Page collection class for locating pages on demand.
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Repository
 */
class PageCollection implements \ArrayAccess, \IteratorAggregate, \Countable {
	/**
	 * @var \Phile\Model\Page[] array of pages found
	 */
    private $pages;

	/**
	 * @var array array of search options
	 */
    private $options;

	/**
	 * @var string base search folder
	 */
    private $folder;

	/**
	 * @var \Phile\Repository\Page page repository
	 */
    private $repository;

    public function __construct(array $options, $folder, \Phile\Repository\Page $repository){
        $this->options = $options;
        $this->folder = $folder;
        $this->repository = $repository;
    }

    public function getIterator(){
        $this->load();
        return new \ArrayIterator($this->pages);
    }

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

    public function count(){
        $this->load();
        return count($this->pages);
    }

    private function load(){
        if ($this->pages === null){
            $this->findPages();
            if (!empty($this->options['pages_order'])){
                $this->sortPages();
            }
        }
    }

    private function findPages(){
		// ignore files with a leading '.' in its filename
		$files = Utility::getFiles($this->folder, '\Phile\FilterIterator\ContentFileFilterIterator');
		$this->pages = [];
		foreach ($files as $file) {
			if (str_replace($this->folder, '', $file) == '404' . CONTENT_EXT) {
				// jump to next page if file is the 404 page
				continue;
			}
			$this->pages[] = $this->repository->getPage($file, $this->folder);
		}
    }

    private function sortPages(){
        $criteria = $this->getSortCriteria();

		// prepare search criteria for array_multisort
		foreach ($criteria as $sort) {
			$key = $sort['key'];
			$column = array();
			foreach ($this->pages as $page) {
				/** @var \Phile\Model\Page $page */
				$meta = $page->getMeta();
				if ($sort['type'] === 'page') {
					$method = 'get' . ucfirst($key);
					$value = $page->$method();
				} elseif ($sort['type'] === 'meta') {
					$value = $meta->get($key);
				} else {
					continue 2; // ignore unhandled search term
				}
				$column[] = $value;
			}
			$sortHelper[] = $column;
			$sortHelper[] = constant('SORT_' . strtoupper($sort['order']));
		}

		$sortHelper[] = &$this->pages;

		call_user_func_array('array_multisort', $sortHelper);
    }

    private function getSortCriteria(){
        // parse search criteria
		$terms = preg_split('/\s+/', $this->options['pages_order'], -1, PREG_SPLIT_NO_EMPTY);
        $criteria = [];
		foreach ($terms as $term) {
			$term = explode('.', $term);
			if (count($term) > 1) {
				$type = array_shift($term);
			} else {
				$type = null;
			}
			$term = explode(':', $term[0]);
			$criteria[] = array('type' => $type, 'key' => $term[0], 'order' => $term[1]);
		}

        return $criteria;
    }
}
