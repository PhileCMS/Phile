<?php
/**
 * @author PhileCMS
 * @link https://philecms.github.io/
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin
 */

namespace Phile\Plugin;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Exception\PluginException;

/**
 * Class PluginRepository manages plugin loading
 */
class PluginRepository
{
    /**
     * @var array array of \Phile\Plugin\PluginDirectory
     */
    protected $directories = [];

    /**
     * @var Event the event-bus
     */
    protected $eventBus;

    public function __construct(Event $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * Adds plugin directory to repository
     *
     * @param string $directory path to plugin-directory
     * @return self
     */
    public function addDirectory(string $directory): self
    {
        $this->directories[] = new PluginDirectory($directory);
        return $this;
    }

    /**
     * Loads all plug-ins
     *
     * @param Config $config phile config with plugin config
     * @throws \Phile\Exception\PluginException
     */
    public function load(Config $config): void
    {
        $errors = $plugins = [];
        $pluginsToLoad = $config->get('plugins');

        foreach ($pluginsToLoad as $pluginKey => $pluginConfig) {
            if (empty($pluginConfig['active'])) {
                continue;
            }
            try {
                $plugins[$pluginKey] = $this->loadSingle($pluginKey, $config);
            } catch (PluginException $e) {
                $errors[] = [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode()
                ];
            }
        }

        $this->eventBus->trigger('plugins_loaded', ['plugins' => $plugins]);

        // Throw after 'plugins_loaded' so that error handler service is set.
        // Even with setupErrorHandler() not run yet, the global app try/catch
        // block uses the handler if present.
        if (count($errors) > 0) {
            throw new PluginException($errors[0]['message'], $errors[0]['code']);
        }

        // settings include initialized plugin-configs now
        $this->eventBus->trigger(
            'config_loaded',
            ['config' => $config->toArray(), 'class' => $config]
        );
    }

    /**
     * Loads and returns single plugin
     *
     * @param string $pluginKey plugin key e.g. 'phile\errorHandler'
     * @return AbstractPlugin Plugin class
     * @throws PluginException
     */
    protected function loadSingle(string $pluginKey, Config $config): AbstractPlugin
    {
        $plugin = $directory = null;
        foreach ($this->directories as $directory) {
            $plugin = $directory->newPluginInstance($pluginKey);
            if ($plugin) {
                break;
            }
        }
        if ($plugin === null) {
            throw new PluginException(
                "the plugin '{$pluginKey}' could not be loaded!",
                1398536479
            );
        }
        $plugin->initializePlugin($pluginKey, $directory->getPath(), $this->eventBus, $config);

        return $plugin;
    }
}
