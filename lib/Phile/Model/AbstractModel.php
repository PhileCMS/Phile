<?php

namespace Phile\Model;

/**
 * Abstract model which implements the ArrayAccess interface
 *
 * @author Frank Nägler
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */
class AbstractModel implements \ArrayAccess {
	protected $data  = array();

	public function get($key) {
		$key = ucfirst($key);
		return (isset($this->data[$key])) ? $this->data[$key] : null;
	}

	public function getAll() {
		return $this->data;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
	}

	public function __get($name) {
		return $this->get($name);
	}

	public function __set($name, $value) {
		$this->set($name, $value);
	}

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
	 * @param mixed $offset
	 *                      An offset to check for.
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
	 * @param mixed $offset
	 *                      The offset to retrieve.
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
	 * @param mixed $offset
	 *                      The offset to assign the value to.
	 * @param mixed $value
	 *                      The value to set.
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
	 * @param mixed $offset
	 *                      The offset to unset.
	 * @return void
	 */
	public function offsetUnset($offset) {
		if ($this->offsetExists($offset)) {
			unset($this->data[$offset]);
		}
	}
}
