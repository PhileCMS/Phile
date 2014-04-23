<?php
/**
 * ServiceLocator MetaParser interface
 */
namespace Phile\ServiceLocator;

/**
 * Interface MetaInterface
 *
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\ServiceLocator
 */
interface MetaInterface {
	/**
	 * parse the content
	 *
	 * @param $rawData
	 *
	 * @return array with key/value store
	 */
	public function parse($rawData);
}
