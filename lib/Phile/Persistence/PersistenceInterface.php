<?php

namespace Phile\Persistence;

interface PersistenceInterface {
	public function has($key);
	public function get($key);
	public function set($key, $value);
	public function delete($key);
}
