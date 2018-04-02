<?php
/**
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin
 */

namespace Phile\Plugin;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Utility;
use Phile\Gateway\EventObserverInterface;

/**
 * the AbstractPlugin class for implementing a plugin for PhileCMS
 */
abstract class AbstractPlugin implements EventObserverInterface
{
    /**
     * @var array plugin attributes
     */
    private $plugin = [];

    /**
     * @var array subscribed Phile events ['eventName' => 'classMethodToCall']
     */
    protected $events = [];

    /**
     * @var array the plugin settings
     */
    protected $settings = [];

    /**
     * Initializes the plugin.
     *
     * try to keep all initialization in one method to have a clean class
     * for the plugin-user
     *
     * @param string $pluginKey
     * @param string $pluginDir Root plugin directory this plugin is placed in.
     * @param Event $eventBus Phile application event-bus.
     * @param Config $config Phile application configuration.
     * @return void
     */
    final public function initializePlugin(
        string $pluginKey,
        string $pluginDir,
        Event $eventBus,
        Config $config
    ): void {
        /**
         * init $plugin property
         */
        $this->plugin['key'] = $pluginKey;
        list($vendor, $name) = explode('\\', $this->plugin['key']);
        $DS = DIRECTORY_SEPARATOR;
        $this->plugin['dir'] = $pluginDir . $vendor . $DS . $name . $DS;

        /**
         * init events
         */
        foreach ($this->events as $event => $method) {
            $eventBus->register($event, $this);
        }

        /**
         * init plugin settings
         */
        $defaults = Utility::load($this->getPluginPath('config.php'));
        if (empty($defaults) || !is_array($defaults)) {
            $defaults = [];
        }

        $globals = $config->toArray();
        if (!isset($globals['plugins'][$pluginKey])) {
            $globals['plugins'][$pluginKey] = [];
        }

        // settings precedence: global > default > class
        $this->settings = array_replace_recursive(
            $this->settings,
            $defaults,
            $globals['plugins'][$pluginKey]
        );

        // backwards compatibility to Phile 1.4
        $this->injectSettings($this->settings);

        $globals['plugins'][$pluginKey]['settings'] = $this->settings;
        $config->set($globals);
    }

    /**
     * inject settings
     *
     * backwards compatibility to Phile 1.4
     *
     * @param array $settings
     * @return void
     * @deprecated since 1.5.1 will be removed
     */
    public function injectSettings(array $settings = null): void
    {
    }

    /**
     * implements EventObserverInterface
     *
     * @param string $eventKey
     * @param null|array $eventData
     * @return void
     */
    public function on($eventKey, $eventData = null): void
    {
        if (!isset($this->events[$eventKey])) {
            return;
        }
        $method = $this->events[$eventKey];
        if (!is_callable([$this, $method])) {
            $class = get_class($this);
            throw new \RuntimeException(
                "Event $eventKey can't invoke $class::$method(). Not callable.",
                1428564865
            );
        }
        $this->{$this->events[$eventKey]}($eventData);
    }

    /**
     * get file path to plugin root (trailing slash) or to a sub-item
     *
     * @param  string $subPath
     * @return string
     */
    protected function getPluginPath(string $subPath = ''): string
    {
        return $this->plugin['dir'] . ltrim($subPath, DIRECTORY_SEPARATOR);
    }
}
