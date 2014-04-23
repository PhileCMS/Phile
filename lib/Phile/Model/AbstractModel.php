<?php
/**
 * the abstract base model
 */
namespace Phile\Model;

/**
 * Abstract model which implements the ArrayAccess interface
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Model
 */
class AbstractModel implements \ArrayAccess {
	/** @var array the storage */
	protected $data = array();

	/**
	 * get value for given key
	 *
	 * @param $key
	 *
	 * @return null|mixed
	 */
	public function get($key) {
		return (isset($this->data[$key])) ? $this->data[$key] : null;
	}

	/**
	 * get all entries
	 *
	 * @return array
	 */
	public function getAll() {
		return $this->data;
	}

	/**
	 * set value for given key
	 *
	 * @param string $key   the key
	 * @param mixed  $value the value
	 */
	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	/**
	 * magic method to get value
	 *
	 * @param $name
	 *
	 * @return null|mixed
	 */
	public function __get($name) {
		return $this->get($name);
	}

	/**
	 * magic method to set value
	 *
	 * @param string $name
	 * @param mixed  $value
	 */
	public function __set($name, $value) {
		$this->set($name, $value);
	}

	/**
	 * magic method to access properties by getter / setter
	 *
	 * @param string $name the name of method
	 * @param array  $args the arguments of the method
	 *
	 * @return mixed|null|void
	 */
	public function __call($name, $args) {
		if (strpos($name, 'get') !== false) {
			$name = substr($name, 3);

			return $this->get($name);
		}
		if (strpos($name, 'set') !== false) {
			$name = substr($name, 3);

			return $this->set($name, $args[0]);
		}

	}

	/**
	 * (PHP 5 >= 5.0.0)
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset
	 *                      An offset to check for.
	 *
	 * @return boolean true on success or false on failure.
	 *                      The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists($offset) {
		return (isset($this->data[$offset]));
	}

	/**
	 * (PHP 5 >= 5.0.0)
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset
	 *                      The offset to retrieve.
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/**
	 * (PHP 5 >= 5.0.0)
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset
	 *                      The offset to assign the value to.
	 * @param mixed $value
	 *                      The value to set.
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		$this->set($offset, $value);
	}

	/**
	 * (PHP 5 >= 5.0.0)
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset
	 *                      The offset to unset.
	 *
	 * @return void
	 */
	public function offsetUnset($offset) {
		if ($this->offsetExists($offset)) {
			unset($this->data[$offset]);
		}
	}
}
