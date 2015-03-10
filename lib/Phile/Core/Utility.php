<?php
/**
 * The Phile Utility class
 */
namespace Phile\Core;

/**
 * the Registry class for implementing a registry
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Utility {

	/**
	 * method to get the current http protocol
	 *
	 * @return string the current protocol
	 */
	public static function getProtocol() {
		if (PHILE_CLI_MODE) {
			return '';
		}
		$protocol = 'http';
		if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ) {
			$protocol = 'https';
		}

		return $protocol;
	}

	/**
	 * detect base url
	 *
	 * @return string
	 */
	public static function getBaseUrl() {
		if (PHILE_CLI_MODE) {
			return '';
		}
		if (Registry::isRegistered('Phile_Settings')) {
			$config = Registry::get('Phile_Settings');
			if (isset($config['base_url']) && $config['base_url']) {
				return $config['base_url'];
			}
		}

		$url         = '';
		$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
		$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
		if ($request_url != $script_url) {
			$url = trim(preg_replace('/' . str_replace('/', '\/', str_replace('index.php', '', $script_url)) . '/', '', $request_url, 1), '/');
		}

		$protocol = self::getProtocol();

		return rtrim(str_replace($url, '', $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), '/');
	}

	/**
	 * detect install path
	 *
	 * @return string
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
	 *
	 * @return bool
	 */
	public static function isPluginLoaded($plugin) {
		$config = \Phile\Registry::get('Phile_Settings');
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
	 */
	public static function redirect($url, $statusCode = 302) {
		header('Location: ' . $url, true, $statusCode);
		die();
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
