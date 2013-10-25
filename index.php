<?php

define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('CONTENT_DIR', ROOT_DIR .'content/');
define('CONTENT_EXT', '.md');
define('LIB_DIR', ROOT_DIR .'lib/');
define('PLUGINS_DIR', ROOT_DIR .'plugins/');
define('THEMES_DIR', ROOT_DIR .'themes/');
define('CACHE_DIR', LIB_DIR .'cache/');

require(ROOT_DIR .'vendor/autoload.php');
// @TODO: implement autoloader for core classes
require(LIB_DIR .'phile.php');
require(LIB_DIR .'Phile/Registry.php');
require(LIB_DIR .'Phile/Model/AbstractModel.php');
require(LIB_DIR .'Phile/Model/Meta.php');
require(LIB_DIR .'Phile/Model/Page.php');
require(LIB_DIR .'Phile/Repository/Page.php');

$phile = new Phile();
