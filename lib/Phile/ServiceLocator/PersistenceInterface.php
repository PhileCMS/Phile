<?php

namespace Phile\ServiceLocator;

/**
 * Interface PersistenceInterface
 *
 * @package Phile\ServiceLocator
 */
interface PersistenceInterface {
	/**
	 * check if an entry exists for given key
	 * @param $key
	 *
	 * @return mixed
	 */
	public function has($key);

	/**
	 * get the entry by given key
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function get($key);

	/**
	 * set the value for given key
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return mixed
	 */
	public function set($key, $value);

	/**
	 * delete the entry by given key
	 * @param $key
	 *
	 * @return mixed
	 */
	public function delete($key);
}
