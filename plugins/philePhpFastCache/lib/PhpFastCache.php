<?php

namespace Phile\Cache;


class PhpFastCache implements CacheInterface {
	/**
	 * @var \phpFastCache
	 */
	protected $cacheEngine;

	public function __construct(\phpFastCache $cacheEngine) {
		$this->cacheEngine = $cacheEngine;
	}

	public function has($key) {
		return ($this->cacheEngine->get($key) !== null);
	}

	public function get($key) {
		return $this->cacheEngine->get($key);
	}

	public function set($key, $value, $time = 300, array $options = array()) {
		$this->cacheEngine->set($key, $value, $time, $options);
	}

	public function delete($key, array $options = array()) {
		$this->cacheEngine->delete($key, $options);
	}

} 