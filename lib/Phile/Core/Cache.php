<?php
/**
 * The Cache class
 */
namespace Phile\Core;

use Phile\ServiceLocator\CacheInterface;

/**
 * Static facade for accessing the cache-service.
 *
 * If no cache engine is defined calls will have no effect.
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class Cache {

	/**
	 * Get cache key.
	 *
	 * @param string $key cache key
	 * @return mixed|null cached value or null if not cached
	 */
	public static function get($key) {
		$engine = static::engine();
		if ($engine === null || !$engine->has($key)) {
			return null;
		}
		return $engine->get($key);
	}

	/**
	 * Cache value.
	 *
	 * @param string $key cache key
	 * @param mixed $value value to cache
	 * @return void
	 */
	public static function set($key, $value) {
		$engine = static::engine();
		if ($engine === null) {
			return;
		}
		$engine->set($key, $value);
	}

	/**
	 * Read-through cache: get cached or execute callback and cache result.
	 *
	 * @param string $key cache key
	 * @param callable $callback callback for generating cache value
	 * @return mixed
	 */
	public static function remember($key, callable $callback) {
		$cached = static::get($key);
		if ($cached !== null) {
			return $cached;
		}
		$result = call_user_func($callback);
		static::set($key, $result);
		return $result;
	}

	/**
	 * Delete cache value.
	 *
	 * @param string $key cache key
	 * @return void
	 */
	public static function delete($key) {
		$engine = static::engine();
		if ($engine === null) {
			return;
		}
		$engine->delete($key);
	}

	/**
	 * Clean whole cache.
	 *
	 * @return void
	 */
	public static function clean() {
		$engine = static::engine();
		if ($engine === null) {
			return;
		}
		$engine->clean();
	}

	/**
	 * Get cache engine instance.
	 *
	 * @return CacheInterface|null
	 */
	protected static function engine() {
		if (!ServiceLocator::hasService('Phile_Cache')) {
			return null;
		}
		return ServiceLocator::getService('Phile_Cache');
	}
}
