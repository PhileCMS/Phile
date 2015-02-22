<?php

/**
 * the Request class
 */

namespace Phile\Core;

/**
 * the Request class gives access to the incoming HTTP request
 *
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Request {

	/**
	 * get request-URL relative to Phile base-URL
	 *
	 * @return string relative URL e.g. `index`, `sub/index`, `sub/page`
	 */
	public static function getUrl() {
		$url = $_SERVER['REQUEST_URI'];

		// remove query string
		$queryPosition = strpos($url, '?');
		if ($queryPosition) {
			$url = substr($url, 0, $queryPosition);
		}

		// resolve root-relative URL-path
		$baseUrl = Router::getBaseUrl();
		$basePath = static::getUrlPath($baseUrl);
		$url = str_replace($basePath, '', $url);
		$url = ltrim($url, '/');

		$url = urldecode($url);

		return $url;
	}

	/**
	 * returns $key in the data send by GET or POST request
	 *
	 * @param string $key
	 * @return null|string
	 */
	public static function getData($key) {
		if (isset($_GET[$key])) {
			$value = $_GET[$key];
		} elseif (isset($_POST[$key])) {
			$value = $_POST[$key];
		} else {
			return null;
		}
		$value = urldecode($value);
		return $value;
	}

	/**
	 * get the HTTP-protocol of the request
	 *
	 * @return string
	 */
	public static function getProtocol() {
		if (empty($_SERVER['HTTP_HOST'])) {
			return null;
		}
		$protocol = 'http';
		if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
			$protocol = 'https';
		}
		return $protocol;
	}

	/**
	 * get path of an URL
	 *
	 * `scheme://host/path/sub` --> `/path/sub`
	 *
	 * @param string $url
	 * @return string
	 */
	protected static function getUrlPath($url) {
		$path = '';
		if (strpos($url, '://') !== false) {
			$parsed = parse_url($url);
			if (isset($parsed['path'])) {
				$path = $parsed['path'];
			}
		}
		return $path;
	}

}
