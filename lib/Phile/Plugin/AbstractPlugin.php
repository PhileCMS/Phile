<?php

namespace Phile;

/**
 * the Registry class for implementing a registry
 * @author Frank Nägler
 *
 */
class Utility {

	/**
	 * @return string the current protocol
	 */
	public static function getProtocol() {
		$protocol = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
			$protocol = 'https';
		}
		return $protocol;
	}
}
