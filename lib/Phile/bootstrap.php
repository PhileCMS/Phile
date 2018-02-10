<?php
/**
 * Phile
 *
 * @author PhileCMS
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 */

/**
 * initialize the global definitions
 */
// phpcs:disable PSR1.Files.SideEffects
defined('PHILE_VERSION') || define('PHILE_VERSION', '1.8.0');
defined('PHILE_CLI_MODE') || define('PHILE_CLI_MODE', (php_sapi_name() === 'cli'));
defined('DS') || define('DS', DIRECTORY_SEPARATOR);
defined('ROOT_DIR') || define('ROOT_DIR', realpath(__DIR__ . DS . '..' . DS . '..' . DS) . DS);
defined('LIB_DIR') || define('LIB_DIR', ROOT_DIR . 'lib' . DS);
defined('PLUGINS_DIR') || define('PLUGINS_DIR', ROOT_DIR . 'plugins' . DS);
defined('THEMES_DIR') || define('THEMES_DIR', ROOT_DIR . 'themes' . DS);
defined('CACHE_DIR') || define('CACHE_DIR', LIB_DIR . 'cache' . DS);
defined('STORAGE_DIR') || define('STORAGE_DIR', LIB_DIR . 'datastorage' . DS);
// phpcs:enable

/**
 * initialize the autoloader
 */
// load from phile core
spl_autoload_register(function ($className) {
    $fileName = LIB_DIR . str_replace("\\", DS, $className) . '.php';
    if (file_exists($fileName)) {
        require_once $fileName;
    }
});
// load from composer
require(LIB_DIR . 'vendor' . DS . 'autoload.php');
