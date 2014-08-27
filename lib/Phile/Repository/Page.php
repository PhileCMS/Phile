<?php
/**
 * the page repository class
 */
namespace Phile\Repository;

use Phile\Exception;
use Phile\ServiceLocator;
use Phile\Utility;


/**
 * the Repository class for pages
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Repository
 */
class Page {
	/**
	 * @var array the settings array
	 */
	protected $settings;

	/**
	 * @var array object storage for initialized objects, to prevent multiple loading of objects.
	 */
	protected $storage = array();

	/**
	 * @var \Phile\ServiceLocator\CacheInterface the cache implementation
	 */
	protected $cache = null;

	/**
	 * the constructor
	 */
	public function __construct($settings = null) {
		if ($settings === null) {
			$settings = \Phile\Registry::get('Phile_Settings');
		}
		$this->settings = $settings;
		if (ServiceLocator::hasService('Phile_Cache')) {
			$this->cache = ServiceLocator::getService('Phile_Cache');
		}
	}

	/**
	 * find a page by path
	 *
	 * @param string $path
	 * @param string $folder
	 *
	 * @return null|\Phile\Model\Page
	 */
	public function findByPath($path, $folder = CONTENT_DIR) {
		$path = str_replace(Utility::getInstallPath(), '', $path);
		$fullPath =  str_replace(array("\\", "//", "\\/", "/\\"), DIRECTORY_SEPARATOR, $folder.$path);

		$file = $fullPath . CONTENT_EXT;

		// append '/index' to full path if file not found
		if (!file_exists($file)) {
			$file = $fullPath . '/index' . CONTENT_EXT;
		}

		return (file_exists($file)) ? $this->getPage($file, $folder) : null;
	}

	/**
	 * find all pages (*.md) files and returns an array of Page models
	 *
	 * @param array  $options
	 * @param string $folder
	 *
	 * @return array of \Phile\Model\Page objects
	 * @throws \Phile\Exception
	 */
	public function findAll(array $options = array(), $folder = CONTENT_DIR) {
		$options += $this->settings;
		// ignore files with a leading '.' in its filename
		$files = Utility::getFiles($folder, '/^.[^\.]*\\' . CONTENT_EXT . '/');
		$pages = array();
		foreach ($files as $file) {
			if (str_replace($folder, '', $file) == '404' . CONTENT_EXT) {
				// jump to next page if file is the 404 page
				continue;
			}
			$pages[] = $this->getPage($file, $folder);
		}

		if (empty($options['pages_order'])) {
			return $pages;
		}

		// parse search criteria
		$terms = preg_split('/\s+/', $options['pages_order'], -1, PREG_SPLIT_NO_EMPTY);
		foreach ($terms as $term) {
			$term = explode('.', $term);
			if (count($term) > 1) {
				$type = array_shift($term);
			} else {
				$type = null;
			}
			$term = explode(':', $term[0]);
			$sorting[] = array('type' => $type, 'key' => $term[0], 'order' => $term[1]);
		}

		// prepare search criteria for array_multisort
		foreach ($sorting as $sort) {
			$key = $sort['key'];
			$column = array();
			foreach ($pages as $page) {
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
		$sortHelper[] = &$pages;

		call_user_func_array('array_multisort', $sortHelper);

		return $pages;
	}

	/**
	 * return page at offset from $page in applied search order
	 *
	 * @param \Phile\Model\Page $page
	 * @param int $offset
	 * @return null|\Phile\Model\Page
	 */
	public function getPageOffset(\Phile\Model\Page $page, $offset = 0) {
		$pages = $this->findAll();
		$order = array();
		foreach ($pages as $p) {
			$order[] = $p->getFilePath();
		}
		$key = array_search($page->getFilePath(), $order) + $offset;
		if (!isset($order[$key])) {
			return null;
		}
		return $this->getPage($order[$key]);
	}

	/**
	 * get page from cache or filepath
	 *
	 * @param        $filePath
	 * @param string $folder
	 *
	 * @return mixed|\Phile\Model\Page
	 */
	protected function getPage($filePath, $folder = CONTENT_DIR) {
		$key = 'Phile_Model_Page_' . md5($filePath);
		if (isset($this->storage[$key])) {
			return $this->storage[$key];
		}

		if ($this->cache !== null) {
			if ($this->cache->has($key)) {
				$page = $this->cache->get($key);
			} else {
				$page = new \Phile\Model\Page($filePath, $folder);
				$this->cache->set($key, $page);
			}
		} else {
			$page = new \Phile\Model\Page($filePath, $folder);
		}
		$this->storage[$key] = $page;

		return $page;
	}

	/**
	 * usort function for Titles Asc
	 *
	 * @param \Phile\Model\Page $a
	 * @param \Phile\Model\Page $b
	 *
	 * @return int
	 */
	protected function compareByTitleAsc(\Phile\Model\Page $a, \Phile\Model\Page $b) {
		$al = strtolower($a->getMeta()->get('title'));
		$bl = strtolower($b->getMeta()->get('title'));
		if ($al == $bl) {
			return 0;
		}

		return ($al > $bl) ? +1 : -1;
	}

	/**
	 * usort function for Titles Desc
	 *
	 * @param \Phile\Model\Page $a
	 * @param \Phile\Model\Page $b
	 *
	 * @return int
	 */
	protected function compareByTitleDesc(\Phile\Model\Page $a, \Phile\Model\Page $b) {
		$al = strtolower($a->getMeta()->get('title'));
		$bl = strtolower($b->getMeta()->get('title'));
		if ($al == $bl) {
			return 0;
		}

		return ($al < $bl) ? +1 : -1;
	}
}
