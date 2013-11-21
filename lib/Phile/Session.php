<?php

namespace Phile;

/**
 * the Session class for implementing a session
 * @author Frank Nägler
 *
 */
class Session {
	static public $isStarted = false;
	static public $sessionId = '';

	static public function start() {
		if (self::$isStarted === false) {
			session_cache_limiter('private');
			session_cache_expire(120);
			if (session_start()) {
				self::$isStarted = true;
			}
			if (self::$isStarted) {
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

	static public function destroy() {
		unset($_SESSION);
		session_destroy();
	}

	static public function save() {
		session_write_close();
	}

	static public function set($key, $value) {
		if (!self::$isStarted) {
			self::start();
		}
		$_SESSION[$key] = $value;
	}

	static public function get($key, $default = null) {
		if (!self::$isStarted) {
			self::start();
		}
		return (self::isEmpty($key)) ? $default : $_SESSION[$key];
	}

	static public function getSessionId() {
		if (!self::$isStarted) {
			self::start();
		}
		return self::$sessionId;
	}

	static public function isEmpty($key) {
		if (!self::$isStarted) {
			self::start();
		}
		return (!isset($_SESSION[$key]));
	}
}
