<?php
/**
 * The Cache class
 */
namespace Phile\Core;

/**
 * lightweight static cache-interface
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class Cache {

	/**
	 * read-through cache
	 *
	 * @param string $key
	 * @param callable $callback
	 * @return mixed
	 */
	public static function remember($key, callable $callback) {
		$cache = null;
		if (ServiceLocator::hasService('Phile_Cache')) {
			$cache = ServiceLocator::getService('Phile_Cache');
		}
		if ($cache && $cache->has($key)) {
			return $cache->get($key);
		}
		$result = call_user_func($callback);
		if ($cache) {
			$cache->set($key, $result);
		}
		return $result;
	}

}

