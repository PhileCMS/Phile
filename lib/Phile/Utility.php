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
	 * detect base url
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
	 * detect install path
	 *
	 * @return string
	 */
	public static function getInstallPath() {
		$path   = self::getBaseUrl();
		$path   = substr($path, strpos($path, '://')+3);
		$path   = substr($path, strpos($path, '/')+1);
		return $path;
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

	/**
	 * load files e.g. config files
	 * @param $file
	 * @return mixed|null
	 */
	public static function load($file) {
		if (file_exists($file)) {
			return include $file;
		}
		return null;
	}

	/**
	 * @param string $directory
	 * @param string $fileNamePattern
	 * @return array
	 */
	public static function getFiles($directory, $fileNamePattern = '/^.*/') {
		$dir        = new \RecursiveDirectoryIterator($directory);
		$ite        = new \RecursiveIteratorIterator($dir);
		$files      = new \RegexIterator($ite, $fileNamePattern, \RegexIterator::GET_MATCH);
		$result     = array();
		foreach ($files as $file) {
			$result[]    = (string) $file[0];
		}
		return $result;
	}

	/**
	 * redirect to an url
	 * @param     $url the url to redirect to
	 * @param int $statusCode the http status code
	 */
	public static function redirect($url, $statusCode = 302) {
		header('Location: ' . $url, true, $statusCode);
		die();
	}
}
