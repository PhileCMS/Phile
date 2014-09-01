<?php

namespace Phile\FilterIterator;

class GeneralFileFilterIterator extends \FilterIterator {
	/**
	 * @return bool
	 */
	public function accept() {
		// accept all kind of files, no filter
		return true;
	}

}