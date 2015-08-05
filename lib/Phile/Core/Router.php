<?php
/**
 * the Router class
 */

namespace Phile\Core;

/**
 * this Router class is responsible for Phile's basic URL management
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class Router {

	/**
	 * @var array with $_SERVER environment
	 */
	protected $server;

	/**
	 * @param array $server $_SERVER environment
	 */
	public function __construct(array $server = []) {
		if (empty($server)) {
			$server = $_SERVER;
		}
		$this->server = $server;
	}

	/**
	 * get request-URL relative to Phile base-URL
	 *
	 * @return string relative URL e.g. `index`, `sub/`, `sub/page`
	 */
	public function getCurrentUrl() {
		$url = $this->server['REQUEST_URI'];

		// remove query string
		$queryPosition = strpos($url, '?');
		if ($queryPosition) {
			$url = substr($url, 0, $queryPosition);
		}

		// resolve root-relative URL-path
		$baseUrl = $this->getBaseUrl();
		$basePath = $this->getUrlPath($baseUrl);
		if (!empty($basePath) && strpos($url, $basePath) === 0) {
			$url = substr($url, strlen($basePath));
		}
		$url = ltrim($url, '/');

		$url = urldecode($url);

		return $url;
	}

	/**
	 * Get base-URL of the Phile installation
	 *
	 * @return string `scheme://host/path/phile-root`
	 */
	public function getBaseUrl() {
		if (Registry::isRegistered('Phile_Settings')) {
			$config = Registry::get('Phile_Settings');
			if (!empty($config['base_url'])) {
				return $config['base_url'];
			}
		}

		$url = '';

		if (isset($this->server['PHP_SELF'])) {
			$url = preg_replace('/index\.php(.*)?$/', '', $this->server['PHP_SELF']);
		}

		if (isset($this->server['HTTP_HOST'])) {
			$host = $this->server['HTTP_HOST'];
			$protocol = $this->getProtocol();
			$url = $protocol . '://' . $host . $url;
		}

		$url = rtrim($url, '/');
		return $url;
	}

	/**
	 * get the URL for a page-Id
	 *
	 * e.g. `sub/index` --> `http://host/phile-root/sub`
	 *
	 * @param string $pageId
	 * @param bool $base return a full or root-relative URL
	 * @return string URL
	 */
	public function urlForPage($pageId, $base = true) {
		$url = $pageId;
		if ($base) {
			$url = $this->url($url);
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
	public function url($url) {
		return $this->getBaseUrl() . '/' . ltrim($url, '/');
	}

	/**
	 * get the HTTP-protocol
	 *
	 * @return string
	 */
	public function getProtocol() {
		if (empty($this->server['HTTP_HOST'])) {
			return null;
		}
		$protocol = 'http';
		if (isset($this->server['HTTPS']) && strtolower($this->server['HTTPS']) !== 'off') {
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
	protected function getUrlPath($url) {
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
