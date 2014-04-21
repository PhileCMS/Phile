<?php

namespace Phile\ServiceLocator;

/**
 * Interface MetaInterface
 *
 * @package Phile\ServiceLocator
 */
interface MetaInterface {
	/**
	 * @param $rawData
	 *
	 * @return array with key/value store
	 */
	public function parse($rawData);
}
