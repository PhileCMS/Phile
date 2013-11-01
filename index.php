<?php

define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('CONTENT_DIR', ROOT_DIR .'content/');
define('CONTENT_EXT', '.md');
define('LIB_DIR', ROOT_DIR .'lib/');
define('PLUGINS_DIR', ROOT_DIR .'plugins/');
define('THEMES_DIR', ROOT_DIR .'themes/');
define('CACHE_DIR', LIB_DIR .'cache/');


spl_autoload_extensions(".php");
spl_autoload_register(function ($className) {
	$fileName = LIB_DIR . str_replace("\\", "/", $className).".php";
	if (file_exists($fileName)) {
		require_once $fileName;
	}
});

require(ROOT_DIR .'vendor/autoload.php');
$phileCore = new \Phile\Core();
