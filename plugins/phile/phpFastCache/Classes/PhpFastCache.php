<?php
/**
 * The PhpFastCache implemenation class
 */
namespace Phile\Plugin\Phile\PhpFastCache;

/**
 * Class PhpFastCache
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\PhpFastCache
 */
class PhpFastCache implements \Phile\ServiceLocator\CacheInterface {
	/**
	 * @var \BasePhpFastCache the cache engine
	 */
	protected $cacheEngine;

	/**
	 * the constructor
	 *
	 * @param \BasePhpFastCache $cacheEngine
	 */
	public function __construct(\BasePhpFastCache $cacheEngine) {
		$this->cacheEngine = $cacheEngine;
	}

	/**
	 * method to check if cache has entry for given key
	 *
	 * @param $key
	 *
	 * @return bool|mixed
	 */
	public function has($key) {
		return ($this->cacheEngine->get($key) !== null);
	}

	/**
	 * method to get cache entry
	 *
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function get($key) {
		return $this->cacheEngine->get($key);
	}

	/**
	 * method to set cache entry
	 *
	 * @param string $key
	 * @param string $value
	 * @param int    $time
	 * @param array  $options
	 *
	 * @return mixed|void
	 */
	public function set($key, $value, $time = 300, array $options = array()) {
		$this->cacheEngine->set($key, $value, $time, $options);
	}

	/**
	 * method to delete cache entry
	 *
	 * @param string $key
	 * @param array  $options
	 *
	 * @return mixed|void
	 */
	public function delete($key, array $options = array()) {
		$this->cacheEngine->delete($key, $options);
	}

	/**
	 * clean complete cache and delete all cached entries
	 */
	public function clean() {
		$this->cacheEngine->clean();
	}

}
