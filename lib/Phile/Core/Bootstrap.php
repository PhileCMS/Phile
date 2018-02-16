<?php
/*
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Core;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\ServiceLocator;
use Phile\Exception\PluginException;
use Phile\Plugin\PluginRepository;

/**
 * Bootstrap class
 */
class Bootstrap
{
    /**
     * Loads $file into $configuration
     */
    public static function loadConfiguration($file, Config $config)
    {
        // function isolates context of loaded files
        $load = function ($file) {
            return include $file;
        };
        $config->merge($load($file));
    }

    /**
     * Creates and protects folder and path $directory
     */
    public static function setupFolder($directory, Config $config)
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
     * Loads all plug-ins
     *
     * @throws Exception\PluginException
     */
    public static function loadPlugins(Event $eventBus, Config $config)
    {
        $pluginsToLoad = $config->get('plugins');

        $loader = new PluginRepository($config->get('plugins_dir'));
        $plugins = $loader->loadAll($pluginsToLoad);
        $errors = $loader->getLoadErrors();

        $eventBus->trigger('plugins_loaded', ['plugins' => $plugins]);

        // throw after 'plugins_loaded'
        if (count($errors) > 0) {
            throw new PluginException($errors[0]['message'], $errors[0]['code']);
        }

        // settings include initialized plugin-configs now
        $eventBus->trigger(
            'config_loaded',
            ['config' => $config->toArray(), 'class' => $config]
        );
    }

    /**
     * Initializes error handling
     */
    public static function setupErrorHandler(Config $config)
    {
        $cliMode = $config->get('phile_cli_mode');
        if (!$cliMode && ServiceLocator::hasService('Phile_ErrorHandler')) {
            $errorHandler = ServiceLocator::getService('Phile_ErrorHandler');
            set_error_handler([$errorHandler, 'handleError']);
            register_shutdown_function([$errorHandler, 'handleShutdown']);
            ini_set('display_errors', $config->get('display_errors'));
        }
    }
}
