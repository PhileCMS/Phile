<?php

namespace Phile\Core;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\ServiceLocator;
use Phile\Exception\PluginException;
use Phile\Plugin\PluginRepository;

/**
 * BaseSetup class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class BaseSetup
{
    /**
     * Initializes all core services and plug-ins
     */
    public static function setUp(Event $eventBus, Config $config)
    {
        self::loadConfiguration($config);
        self::setupPhpEnvironment($config);
        self::setupFolders($config);
        self::loadPlugins($eventBus, $config);
        self::setupErrorHandler($config);
    }

    /**
     * Loads core configuration from configuration files
     */
    protected static function loadConfiguration(Config $config)
    {
        $files = [
            'default' => $config->get('root_dir') . 'default_config.php',
            'local' => $config->get('root_dir') . 'config.php'
        ];
        // function isolates context of loaded files
        $load = function ($file) {
            return include $file;
        };
        foreach ($files as $file) {
            $config->merge($load($file));
        }
    }

    /**
     * Sets additional PHP settings
     */
    protected static function setupPhpEnvironment(Config $config)
    {
        date_default_timezone_set($config->get('timezone'));
    }

    /**
     * Creates and sets up core folders if missing
     */
    protected static function setupFolders(Config $config)
    {
        $dirs = [
            $config->get('cache_dir'),
            $config->get('storage_dir'),
        ];
        foreach ($dirs as $dir) {
            if (empty($dir) || strpos($dir, $config->get('root_dir')) !== 0) {
                continue;
            }
            if (!file_exists($dir)) {
                mkdir($dir, 0775, true);
            }
            $htaccessPath = "$dir.htaccess";
            if (!file_exists($htaccessPath)) {
                $htaccessContent = "order deny,allow\ndeny from all\nallow from 127.0.0.1";
                file_put_contents($htaccessPath, $htaccessContent);
            }
        }
    }

    /**
     * Loads all plug-ins
     *
     * @throws Exception\PluginException
     */
    protected static function loadPlugins(Event $eventBus, Config $config)
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
    protected static function setupErrorHandler(Config $config)
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
