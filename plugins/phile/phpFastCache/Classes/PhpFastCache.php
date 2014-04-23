<?php

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
	 * @var \phpFastCache
	 */
	protected $cacheEngine;

	/**
	 * @param \phpFastCache $cacheEngine
	 */
	public function __construct(\phpFastCache $cacheEngine) {
		$this->cacheEngine = $cacheEngine;
	}

	/**
	 * @param $key
	 *
	 * @return bool|mixed
	 */
	public function has($key) {
		return ($this->cacheEngine->get($key) !== null);
	}

	/**
	 * @param $key
	 *
	 * @return mixed|null
	 */
	public function get($key) {
		return $this->cacheEngine->get($key);
	}

	/**
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
	 * @param string $key
	 * @param array  $options
	 *
	 * @return mixed|void
	 */
	public function delete($key, array $options = array()) {
		$this->cacheEngine->delete($key, $options);
	}

} 