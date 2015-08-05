<?php
/**
 * The Phile Utility class
 */
namespace Phile\Core;

/**
 * Utility class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Utility {

	/**
	 * method to get the current http protocol
	 *
	 * @return string the current protocol
	 * @deprecated since 1.5 will be removed
	 */
	public static function getProtocol() {
		return (new Router)->getProtocol();
	}

	/**
	 * detect base url
	 *
	 * @return string
	 * @deprecated since 1.5 will be removed
	 */
	public static function getBaseUrl() {
		return (new Router)->getBaseUrl();
	}

	/**
	 * detect install path
	 *
	 * @return string
	 * @deprecated since 1.5 will be removed
	 */
	public static function getInstallPath() {
		$path = self::getBaseUrl();
		$path = substr($path, strpos($path, '://') + 3);
		$path = substr($path, strpos($path, '/') + 1);

		return $path;
	}

	/**
	 * resolve a file path by replace the mod: prefix
	 *
	 * @param $path
	 *
	 * @return string|null the full filepath or null if file does not exists
	 */
	public static function resolveFilePath($path) {
		// resolve MOD: prefix
		if (strtoupper(substr($path, 0, 3)) === 'MOD') {
			$path = str_ireplace('mod:', PLUGINS_DIR, $path);
		}
		// check if file exists
		if (file_exists($path)) {
			return $path;
		}

		return null;
	}

	/**
	 * load files e.g. config files
	 *
	 * @param $file
	 *
	 * @return mixed|null
	 */
	public static function load($file) {
		if (file_exists($file)) {
			return include $file;
		}

		return null;
	}

	/**
	 * check if a plugin is loaded
	 *
	 * @param $plugin
	 * @return bool
	 * @deprecated since 1.5 will be removed
	 * @use 'plugins_loaded' event
	 */
	public static function isPluginLoaded($plugin) {
		$config = Registry::get('Phile_Settings');
		return (isset($config['plugins'][$plugin]) && isset($config['plugins'][$plugin]['active']) && $config['plugins'][$plugin]['active'] === true);
	}

	/**
	 * static method to get files by directory and file filter
	 *
	 * @param        $directory
	 * @param string $filter
	 *
	 * @return array
	 */
	public static function getFiles($directory, $filter = '\Phile\FilterIterator\GeneralFileFilterIterator') {
		$files = new $filter(
			new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(
					$directory,
					\RecursiveDirectoryIterator::FOLLOW_SYMLINKS
				)
			)
		);
		$result = array();
		foreach ($files as $file) {
			/** @var \SplFileInfo $file */
			$result[] = $file->getPathname();
		}

		return $result;
	}

	/**
	 * redirect to an url
	 *
	 * @param     $url        the url to redirect to
	 * @param int $statusCode the http status code
	 * @deprecated since 1.5 will be removed
	 */
	public static function redirect($url, $statusCode = 302) {
		(new Response)->redirect($url, $statusCode);
	}

	/**
	 * generate secure md5 hash
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public static function getSecureMD5Hash($value) {
		$config = Registry::get('Phile_Settings');

		return md5($config['encryptionKey'] . $value);
	}

	/**
	 * method to generate a secure token
	 * code from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
	 * modified by Frank Nägler
	 *
	 * @param int  $length
	 * @param bool $widthSpecialChars
	 * @param null $additionalChars
	 *
	 * @return string
	 */
	public static function generateSecureToken($length = 32, $widthSpecialChars = true, $additionalChars = null) {
		$token        = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet .= "0123456789";
		if ($widthSpecialChars) {
			$codeAlphabet .= "!/()=?[]|{}";
		}
		if ($additionalChars !== null) {
			$codeAlphabet .= $additionalChars;
		}
		for ($i = 0; $i < $length; $i++) {
			$token .= $codeAlphabet[Utility::crypto_rand_secure(0, strlen($codeAlphabet))];
		}

		return $token;
	}

	/**
	 * method to get a more secure random value
	 * code from http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string/13733588#13733588
	 *
	 * @param $min
	 * @param $max
	 *
	 * @return mixed
	 */
	public static function crypto_rand_secure($min, $max) {
		$range = $max - $min;
		if ($range < 0) {
			return $min;
		} // not so random...
		$log    = log($range, 2);
		$bytes  = (int)($log / 8) + 1; // length in bytes
		$bits   = (int)$log + 1; // length in bits
		$filter = (int)(1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd >= $range);

		return $min + $rnd;
	}
}
