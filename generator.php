<?php
/**
 * @author Frank Nägler
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */

define('ROOT_DIR',         realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('CONTENT_DIR',      ROOT_DIR . 'content' . DIRECTORY_SEPARATOR);
define('CONTENT_EXT',      '.md');
define('LIB_DIR',          ROOT_DIR . 'lib' . DIRECTORY_SEPARATOR);
define('PLUGINS_DIR',      ROOT_DIR . 'plugins' . DIRECTORY_SEPARATOR);
define('THEMES_DIR',       ROOT_DIR . 'themes' . DIRECTORY_SEPARATOR);
define('CACHE_DIR',        LIB_DIR . 'cache' . DIRECTORY_SEPARATOR);


spl_autoload_extensions(".php");
spl_autoload_register(function ($className) {
	$fileName = LIB_DIR . str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php';
	if (file_exists($fileName)) {
		require_once $fileName;
	}
});

require(LIB_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

echo \Phile\Utility::generateSecureToken(64);
