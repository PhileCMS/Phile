<?php
/**
 * Plugin class
 */
namespace Phile\Plugin;

use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Utility;
use Phile\Gateway\EventObserverInterface;

/**
 * the AbstractPlugin class for implementing a plugin for PhileCMS
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin
 */
abstract class AbstractPlugin implements EventObserverInterface {

	/** @var string plugin attributes */
	protected $plugin = [];

	 /** @var array subscribed Phile events ['eventName' => 'classMethodToCall'] */
	protected $events = [];

	/** @var array the plugin settings */
	protected $settings = [];

	/**
	 * initialize plugin
	 *
	 * try to keep all initialization in one method to have a clean class
	 * for the plugin-user
	 *
	 * @param string $pluginKey
	 * @deprecated since 1.5.1 will be declared 'final'
	 */
	public function initializePlugin($pluginKey) {
		/**
		 * init $plugin property
		 */
		$this->plugin['key'] = $pluginKey;
		list($vendor, $name) = explode('\\', $this->plugin['key']);
		$DS = DIRECTORY_SEPARATOR;
		$this->plugin['dir'] = PLUGINS_DIR . $vendor . $DS . $name . $DS;

		/**
		 * init events
		 */
		foreach ($this->events as $event => $method) {
			Event::registerEvent($event, $this);
		}

		/**
		 * init plugin settings
		 */
		$defaults = Utility::load($this->getPluginPath('config.php'));
		if (empty($defaults) || !is_array($defaults)) {
			$defaults = [];
		}

		$globals = Registry::get('Phile_Settings');
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
		Registry::set('Phile_Settings', $globals);

	}

	/**
	 * inject settings
	 *
	 * backwards compatibility to Phile 1.4
	 *
	 * @param array $settings
	 * @deprecated since 1.5.1 will be removed
	 */
	public function injectSettings(array $settings = null) {
	}

	/**
	 * implements EventObserverInterface
	 *
	 * @param string $eventKey
	 * @param null $data
	 * @return void
	 */
	public function on($eventKey, $data = null) {
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
		$this->{$this->events[$eventKey]}($data);
	}

	/**
	 * get file path to plugin root (trailing slash) or to a sub-item
	 *
	 * @param string $subPath
	 * @return null|string null if item does not exist
	 */
	protected function getPluginPath($subPath = '') {
		return $this->plugin['dir'] . ltrim($subPath, DIRECTORY_SEPARATOR);
	}

}
