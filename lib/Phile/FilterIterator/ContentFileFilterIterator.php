<?php
/**
 * the filter class for content files
 */
namespace Phile\FilterIterator;

/**
 * Class ContentFileFilterIterator
 *
 * @package Phile\FilterIterator
 */
class ContentFileFilterIterator extends \FilterIterator {
	/**
	 * method to decide if file is filterd or not
	 * @return bool
	 */
	public function accept() {
		/** @var \SplFileInfo $this */
		return (preg_match('/^[^\.]{1}.*'.CONTENT_EXT.'/', $this->getFilename()) > 0);
	}

}