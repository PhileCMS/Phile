<?php
/**
 * Plugin class
 */
namespace Phile\Plugin;

use Phile\Core\Event;
use Phile\Gateway\EventObserverInterface;

/**
 * the AbstractPlugin class for implementing a plugin for PhileCMS
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin
 */
abstract class AbstractPlugin implements EventObserverInterface {

	/**
	 * @var array subscribed Phile events ['eventName' => 'classMethodToCall']
	 */
	protected $events = [];

	/**
	 * @var array the plugin settings
	 */
	protected $settings;

	/**
	 * initialize plugin
	 *
	 * @param array $settings
	 */
	public function initialize(array $settings = []) {
		$this->settings = $settings;
		foreach ($this->events as $event => $method) {
			Event::registerEvent($event, $this);
		}
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

}
