<?php
/**
 * NullCache class
 */

namespace Phile\Test;

/**
 * Implements in-memory cache service.
 *
 * Used in testing (no side-effects, performance).
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Test
 */
class NullCache implements \Phile\ServiceLocator\CacheInterface {

	/**
	 * @var int current unix-timestamp
	 */
	protected $now;

	/**
	 * @var array cache storage
	 */
	protected $storage = [];

	/**
	 *  {@inheritdoc}
	 */
	public function __construct() {
		$this->now = time();
	}

	/**
	 *  {@inheritdoc}
	 */
	public function has($key) {
		if (!isset($this->storage[$key])) {
			return false;
		}
		return $this->storage[$key]['expire'] > $this->now;
	}

	/**
	 *  {@inheritdoc}
	 */
	public function get($key) {
		if (!$this->has($key)) {
			throw new \Exception("No cache entry with key: $key");
		}
		return $this->storage[$key]['value'];
	}

	/**
	 *  {@inheritdoc}
	 */
	public function set($key, $value, $time = 300, array $options = array()) {
		$this->storage[$key] = [
			'expire' => time() + $time,
			'value' => $value
		];
	}

	/**
	 *  {@inheritdoc}
	 */
	public function delete($key, array $options = array()) {
		unset($this->storage[$key]);
	}

	/**
	 *  {@inheritdoc}
	 */
	public function clean() {
		$this->storage = [];
	}

}