<?php
/**
 * the filter class for all files
 */
namespace Phile\FilterIterator;
/**
 * Class GeneralFileFilterIterator
 *
 * @package Phile\FilterIterator
 */
class GeneralFileFilterIterator extends \FilterIterator {
	/**
	 * method to decide if file is filterd or not
	 * @return bool
	 */
	public function accept() {
		// accept all kind of files, no filter
		return true;
	}

}