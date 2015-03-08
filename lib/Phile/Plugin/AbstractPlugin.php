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

	/** @var string `vendor\pluginName` */
	protected $pluginKey;

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
	 */
	public function initializePlugin($pluginKey) {
		$this->pluginKey = $pluginKey;

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

		$globals['plugins'][$pluginKey]['settings'] = $this->settings;
		Registry::set('Phile_Settings', $globals);
	}

	/**
	 * implements EventObserverInterface
	 *
	 * @param string $eventKey
	 * @param null $data
	 * @return mixed|void
	 */
	public function on($eventKey, $data = null) {
		if (isset($this->events[$eventKey]) && is_callable([$this, $this->events[$eventKey]])) {
			$this->{$this->events[$eventKey]}($data);
		}
	}

	/**
	 * get file path to plugin root (trailing slash) or to a sub-item
	 *
	 * @param string $subPath
	 * @return null|string null if item does not exist
	 */
	protected function getPluginPath($subPath = '') {
		$ds = DIRECTORY_SEPARATOR;
		list($vendor, $name) = explode('\\', $this->pluginKey);
		return PLUGINS_DIR . $vendor . $ds . $name . $ds . ltrim($subPath,
			$ds);
	}

}
