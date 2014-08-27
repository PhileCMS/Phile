<?php

namespace Phile\FilterIterator;

class GeneralFileFilterIterator extends \FilterIterator {
	/**
	 * @return bool
	 */
	public function accept() {
		/** @var \SplFileInfo $this */
		return (preg_match('/^.*/', $this->getFilename()) > 0);
	}

}