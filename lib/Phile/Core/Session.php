<?php
/**
 * The Session class
 */
namespace Phile\Core;

/**
 * the Session class for implementing a session
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class Session {
	/** @var bool mark if session is started */
	static public $isStarted = false;

	/** @var string the session id */
	static public $sessionId = '';

	/**
	 * method to start the session
	 */
	static public function start() {
		if (self::$isStarted === false) {
			session_cache_limiter('private');
			session_cache_expire(120);
			if (session_start()) {
				self::$isStarted = true;
			}
			if (self::$isStarted) {
				if (PHILE_CLI_MODE) {
					$_SERVER['REMOTE_ADDR'] = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
				}
				if (self::get('REMOTE_ADDR') != $_SERVER['REMOTE_ADDR']) {
					session_destroy();
					session_start();
					session_regenerate_id();
					self::set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
				}
				if (self::get('REMOTE_ADDR') === null) {
					self::set('REMOTE_ADDR', $_SERVER['REMOTE_ADDR']);
				}
			}
		}
		self::$sessionId = session_id();
	}

	/**
	 * method to destroy the session
	 */
	static public function destroy() {
		unset($_SESSION);
		session_destroy();
	}

	/**
	 * method to save and close the session
	 */
	static public function save() {
		session_write_close();
	}

	/**
	 * method to set value into session
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	static public function set($key, $value) {
		if (!self::$isStarted) {
			self::start();
		}
		$_SESSION[$key] = $value;
	}

	/**
	 * method to get value from session
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return null|mixed
	 */
	static public function get($key, $default = null) {
		if (!self::$isStarted) {
			self::start();
		}

		return (self::isEmpty($key)) ? $default : $_SESSION[$key];
	}

	/**
	 * get the session id
	 *
	 * @return string
	 */
	static public function getSessionId() {
		if (!self::$isStarted) {
			self::start();
		}

		return self::$sessionId;
	}

	/**
	 * check id key is empty/set or not
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	static public function isEmpty($key) {
		if (!self::$isStarted) {
			self::start();
		}

		return (!isset($_SESSION[$key]));
	}
}
