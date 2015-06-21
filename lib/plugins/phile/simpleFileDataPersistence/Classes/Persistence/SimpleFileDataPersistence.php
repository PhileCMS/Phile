<?php
/**
 * persistence implementation class
 */
namespace Phile\Plugin\Phile\SimpleFileDataPersistence\Persistence;

use Phile\ServiceLocator\PersistenceInterface;

/**
 * Class SimpleFileDataPersistence
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\SimpleFileDataPersistence\Persistence
 */
class SimpleFileDataPersistence implements PersistenceInterface {
	/** @var string $dataDirectory the data storage directory */
	protected $dataDirectory;

	/**
	 * the constructor
	 */
	public function __construct() {
		$this->dataDirectory = STORAGE_DIR;
	}

	/**
	 * check if key exists
	 *
	 * @param $key
	 *
	 * @return bool|mixed
	 */
	public function has($key) {
		return (file_exists($this->getStorageFile($key)));
	}

	/**
	 * get value for given key
	 *
	 * @param $key
	 *
	 * @return mixed
	 * @throws \Phile\Exception\AbstractException
	 */
	public function get($key) {
		if (!$this->has($key)) {
			throw new \Phile\Exception\AbstractException("no data storage for key '{$key}' exists!");
		}

		return unserialize(file_get_contents($this->getStorageFile($key)));
	}

	/**
	 * set value for given key
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return mixed|void
	 */
	public function set($key, $value) {
		file_put_contents($this->getStorageFile($key), serialize($value));
	}

	/**
	 * delte given key/index
	 *
	 * @param string $key
	 * @param array  $options
	 *
	 * @return mixed|void
	 * @throws \Phile\Exception\AbstractException
	 */
	public function delete($key, array $options = array()) {
		if (!$this->has($key)) {
			throw new \Phile\Exception\AbstractException("no data storage for key '{$key}' exists!");
		}
		unlink($this->getStorageFile($key));
	}

	/**
	 * generate internal key
	 *
	 * @param $key
	 *
	 * @return string
	 */
	protected function getInternalKey($key) {
		return md5($key);
	}

	/**
	 * get storage filename
	 *
	 * @param $key
	 *
	 * @return string
	 */
	protected function getStorageFile($key) {
		return $this->dataDirectory . $this->getInternalKey($key) . '.ds';
	}
}
