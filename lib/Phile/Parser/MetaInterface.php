<?php

namespace Phile\Parser;

interface MetaInterface {
	/**
	 * @param $rawData
	 *
	 * @return array with key/value store
	 */
	public function parse($rawData);
}
