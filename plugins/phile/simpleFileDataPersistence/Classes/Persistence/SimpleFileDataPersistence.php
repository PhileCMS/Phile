<?php

namespace Phile\Plugin\Phile\SimpleFileDataPersistence\Persistence;

use Phile\Exception;
use Phile\ServiceLocator\PersistenceInterface;

class SimpleFileDataPersistence implements PersistenceInterface {
	protected $dataDirectory;

	public function __construct() {
		$this->dataDirectory = ROOT_DIR . 'datastorage/';
	}

	public function has($key) {
		return (file_exists($this->getStorageFile($key)));
	}

	public function get($key) {
		if (!$this->has($key)) {
			throw new Exception("no data storage for key '{$key}' exists!");
		}
		return unserialize(file_get_contents($this->getStorageFile($key)));
	}

	public function set($key, $value) {
		file_put_contents($this->getStorageFile($key), serialize($value));
	}

	public function delete($key, array $options = array()) {
		if (!$this->has($key)) {
			throw new Exception("no data storage for key '{$key}' exists!");
		}
		unlink($this->getStorageFile($key));
	}

	protected function getInternalKey($key) {
		return md5($key);
	}

	protected function getStorageFile($key) {
		return $this->dataDirectory . $this->getInternalKey($key) . '.ds';
	}
}