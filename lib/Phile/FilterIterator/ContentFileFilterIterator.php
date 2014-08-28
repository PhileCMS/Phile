<?php

namespace Phile\FilterIterator;

class ContentFileFilterIterator extends \FilterIterator {
	/**
	 * @return bool
	 */
	public function accept() {
		/** @var \SplFileInfo $this */
		return (preg_match('/^[^\.]{1}.*'.CONTENT_EXT.'/', $this->getFilename()) > 0);
	}

}