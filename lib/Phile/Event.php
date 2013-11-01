<?php

namespace Phile;

/**
 * the Event class for implementing a hook/event system
 * @author Frank Nägler
 *
 */
class Event {
	/**
	 * Registry object provides storage for objects.
	 * @var array
	 */
	private static $_registry = array();

	/**
	 * @param string                 $eventName the event to observe
	 * @param EventObserverInterface $object
	 */
	public static function registerEvent($eventName, EventObserverInterface $object) {
		if (!isset(self::$_registry[$eventName])) {
			self::$_registry[$eventName] = array();
		}
		self::$_registry[$eventName][] = $object;
	}

	/**
	 * @param string $eventName the event name (register for this name)
	 * @param array  $data array with some additional data
	 */
	public static function triggerEvent($eventName, $data = null) {
		if (isset(self::$_registry[$eventName]) && is_array(self::$_registry[$eventName])) {
			foreach (self::$_registry[$eventName] as $observer) {
				call_user_func_array(array($observer, 'on'), array($eventName, $data));
			}
		}
	}
}
