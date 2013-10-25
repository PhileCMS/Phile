<?php

namespace Phile\Repository;
use Phile\Registry;


/**
 * the Repository class for pages
 * @author Frank Nägler
 * @package Phile\Repository
 */
class Page {
	public function findByPath($path) {
		$config     = Registry::get('Phile_Settings');
		$path       = str_replace($config['install_path'], '', $path);
		$file = null;
		if (file_exists(CONTENT_DIR . $path . CONTENT_EXT)) {
			$file = CONTENT_DIR . $path . CONTENT_EXT;
		}
		if ($file == null) {
			if (file_exists(CONTENT_DIR . $path . '/index' . CONTENT_EXT)) {
				$file = CONTENT_DIR . $path . '/index' . CONTENT_EXT;
			}
		}

		if ($file !== null) {
			return new \Phile\Model\Page($file);
		}

		return null;
	}
} 