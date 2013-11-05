<?php

namespace Phile\Cache;

interface CacheInterface {
	public function has($key);
	public function get($key);
	public function set($key, $value, $time = 300, array $options = array());
}
