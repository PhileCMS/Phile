<?php

/**
 * Global constants
 *
 * @author PhileCMS
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 */

define('PHILE_VERSION', '2.0.0');
define('PHILE_CLI_MODE', (php_sapi_name() === 'cli'));
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', realpath(__DIR__ . DS . '..' . DS) . DS);
define('LIB_DIR', ROOT_DIR . 'lib' . DS);
define('CONFIG_DIR', ROOT_DIR . DS . 'config' . DS);
define('PLUGINS_DIR', ROOT_DIR . 'plugins' . DS);
define('THEMES_DIR', ROOT_DIR . 'themes' . DS);
define('CACHE_DIR', LIB_DIR . 'cache' . DS);
define('STORAGE_DIR', LIB_DIR . 'datastorage' . DS);
