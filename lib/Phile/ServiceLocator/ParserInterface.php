<?php

namespace Phile\ServiceLocator;

/**
 * Interface ParserInterface
 *
 * @package Phile\ServiceLocator
 */
interface ParserInterface {
	/**
	 * parse data
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function parse($data);
}
