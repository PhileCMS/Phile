<?php

define('PHILE_VERSION',    '0.9.2');
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
	} else {
		// autoload plugin namespace
		if (strpos($className, "Phile\\Plugin\\") === 0) {
			$className 		= substr($className, 13);
			$classNameParts = explode('\\', $className);
			$pluginName 	= lcfirst(array_shift($classNameParts));
			$classPath		= array_merge(array($pluginName, 'Classes'), $classNameParts);
			$fileName 		= PLUGINS_DIR . implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
			if (file_exists($fileName)) {
				require_once $fileName;
			}
		}
	}
});

require(ROOT_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

$phileCore = new \Phile\Core();
echo $phileCore->render();
