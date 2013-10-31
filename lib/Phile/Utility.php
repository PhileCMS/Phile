<?php

namespace Phile;

/**
 * the Registry class for implementing a registry
 * @author Frank Nägler
 *
 */
class Utility {

	/**
	 * @return string the current protocol
	 */
	public static function getProtocol() {
		$protocol = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
			$protocol = 'https';
		}
		return $protocol;
	}

	/**
	 * @return string
	 */
	public static function getBaseUrl() {
		if (Registry::isRegistered('Phile_Settings')) {
			$config = Registry::get('Phile_Settings');
			if (isset($config['base_url']) && $config['base_url']) {
				return $config['base_url'];
			}
		}

		$url = '';
		$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
		$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
		if($request_url != $script_url) $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

		$protocol = self::getProtocol();
		return rtrim(str_replace($url, '', $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), '/');
	}

	/**
	 * resolve a file path by replace the mod: prefix
	 *
	 * @param $path
	 * @return string|null the full filepath or null
	 */
	public static function resolveFilePath($path) {
		// resolve MOD: prefix
		if (strtoupper(substr($path, 0, 3)) == 'MOD') {
			$path = str_ireplace('mod:', PLUGINS_DIR, $path);
			if (file_exists($path)) {
				return $path;
			}
		}
		// check if file exists
		if (file_exists($path)) {
			return $path;
		}

		return null;
	}

	public static function load($file) {
		if (file_exists($file)) {
			return include $file;
		}
		return null;
	}
}
