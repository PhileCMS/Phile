<?php

/**
 * the Router class
 */

namespace Phile\Core;

/**
 * this Router class is responsible for Phile's basic URL management
 *
 * @package Phile\Core
 */
class Router {

	/**
	 * get the URL for a page-Id
	 *
	 * e.g. `sub/index` --> `http://host/phile-root/sub`
	 *
	 * @param string $pageId
	 * @param bool $base return a full or root-relative URL
	 * @return string URL
	 */
	public static function urlForPage($pageId, $base = true) {
		$url = static::tidyUrl($pageId);
		if ($base) {
			$url = static::url($url);
		}
		return $url;
	}

	/**
	 * converts Phile-root relative URL to full URL
	 *
	 * e.g. `foo/bar.ext` --> `http://host/phile-root/foo/bar.ext`
	 *
	 * @param string $url
	 * @return string
	 */
	public static function url($url) {
		return static::getBaseUrl() . '/' . ltrim($url, '/');
	}

	/**
	 * Get base-URL of the Phile installation
	 *
	 * @return string `scheme://host/path/phile-root`
	 */
	public static function getBaseUrl() {
		if (Registry::isRegistered('Phile_Settings')) {
			$config = Registry::get('Phile_Settings');
			if (isset($config['base_url']) && $config['base_url']) {
				return $config['base_url'];
			}
		}

		$scriptUrl = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
		$url = str_replace('index.php', '', $scriptUrl);

		if (!isset($_SERVER['HTTP_HOST'])) {
			return $url;
		}
		$host = $_SERVER['HTTP_HOST'];
		$protocol = Request::getProtocol();
		if ($protocol) {
			$url = $protocol . '://' . $host . $url;
		}
		$url = rtrim($url, '/');
		return $url;
	}

	/**
	 * convert URL to canonical Phile-URL
	 *
	 * - remove `/index` suffix (see #170)
	 * - remove trailing slash
	 *
	 * @param string $url
	 * @return string
	 */
	public static function tidyUrl($url) {
		$url = preg_replace('/(^|\/)index(\/)?$/', '', $url);
		$url = rtrim($url, '/');
		return $url;
	}

}
