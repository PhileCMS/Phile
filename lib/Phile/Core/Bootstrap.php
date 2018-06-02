<?php
/*
 * @author  PhileCMS
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Core;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\ServiceLocator;
use Phile\Exception\PluginException;
use Phile\Plugin\PluginRepository;

/**
 * Bootstrap class
 */
class Bootstrap
{
    /**
     * Loads $file into $configuration.
     *
     * @param string $file Path to config file to load
     * @param Config $config Phile configuration
     * @return void
     */
    public static function loadConfiguration($file, Config $config)
    {
        // function isolates context of loaded files
        $load = function (string $file): array {
            return include $file;
        };
        $config->merge($load($file));
    }

    /**
     * Creates and protects folder and path $directory.
     *
     * @param string $directory Path to $directory
     * @param Config $config Phile configuration
     * @return void
     */
    public static function setupFolder(string $directory, Config $config)
    {
        if (empty($directory) || strpos($directory, $config->get('root_dir')) !== 0) {
            return;
        }
        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }
        $htaccessPath = "$directory.htaccess";
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "order deny,allow\ndeny from all\nallow from 127.0.0.1";
            file_put_contents($htaccessPath, $htaccessContent);
        }
    }

    /**
     * Initializes error handling
     *
     * @param Config $config Phile configuration
     * @return void
     */
    public static function setupErrorHandler(Config $config)
    {
        $cliMode = $config->get('phile_cli_mode');
        if ($cliMode || !ServiceLocator::hasService('Phile_ErrorHandler')) {
            return;
        }
        $errorHandler = ServiceLocator::getService('Phile_ErrorHandler');
        set_error_handler([$errorHandler, 'handleError']);
        set_exception_handler([$errorHandler, 'handleException']);
        register_shutdown_function([$errorHandler, 'handleShutdown']);
    }
}
