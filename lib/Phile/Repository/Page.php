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
	const ORDER_ASC  = 'asc';
	const ORDER_DESC = 'desc';

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
	public function __construct() {
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
		$file = null;
		if (file_exists($folder . $path . CONTENT_EXT)) {
			$file = $folder . $path . CONTENT_EXT;
		}
		if ($file == null) {
			if (file_exists($folder . $path . '/index' . CONTENT_EXT)) {
				$file = $folder . $path . '/index' . CONTENT_EXT;
			}
		}

		if ($file !== null) {
			return $this->getPage($file, $folder);
		}

		return null;
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
	public function findAll(array $options = null, $folder = CONTENT_DIR) {
		$files = Utility::getFiles($folder, '/^.*\\' . CONTENT_EXT . '/');
		$pages = array();
		foreach ($files as $file) {
			if (str_replace($folder, '', $file) == '404' . CONTENT_EXT) {
				// jump to next page if file is the 404 page
				continue;
			}
			$pages[] = $this->getPage($file, $folder);
		}

		if ($options !== null && isset($options['pages_order_by'])) {
			switch (strtolower($options['pages_order_by'])) {
				case 'alpha':
				case 'title':
					if (strtolower($options['pages_order_by']) == 'alpha') {
						error_log('the key alpha for sorting is deprecated, use title instead');
					}
					if (!isset($options['pages_order'])) {
						$options['pages_order'] = self::ORDER_ASC;
					}
					if ($options['pages_order'] == self::ORDER_ASC) {
						usort($pages, array($this, "compareByTitleAsc"));
					}
					if ($options['pages_order'] == self::ORDER_DESC) {
						usort($pages, array($this, "compareByTitleDesc"));
					}
					break;
				case 'date':
				default:
					if (strtolower($options['pages_order_by']) == 'date') {
						error_log('the key date for sorting is deprecated use meta:date or any other meta tag');
						$options['pages_order_by'] = 'meta:date';
					}
					if (strpos(strtolower($options['pages_order_by']), 'meta:') !== false) {
						$metaKey      = str_replace('meta:', '', strtolower($options['pages_order_by']));
						$sorted_pages = array();
						foreach ($pages as $page) {
							if ($page->getMeta()->get($metaKey) !== null) {
								$key = '_' . $page->getMeta()->get($metaKey);
								if (array_key_exists($key, $sorted_pages)) {
									$counter = 1;
									$tmp     = $key;
									while (array_key_exists($tmp, $sorted_pages)) {
										$tmp = $key . '_' . $counter++;
									}
									$key = $tmp;
								}
								$sorted_pages[$key] = $page;
							} else {
								$sorted_pages[] = $page;
							}
						}
						if (!isset($options['pages_order'])) {
							$options['pages_order'] = self::ORDER_ASC;
						}
						if ($options['pages_order'] == self::ORDER_ASC) {
							ksort($sorted_pages);
						}
						if ($options['pages_order'] == self::ORDER_DESC) {
							krsort($sorted_pages);
						}
						unset($pages);
						$pages = $sorted_pages;
					} else {
						throw new Exception\RepositoryException("unknown key '{$options['pages_order_by']}' for pages_order_by");
					}
					break;
			}
		}

		return $pages;
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
