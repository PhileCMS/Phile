<?php
/**
 * The Event class
 */
namespace Phile\Core;

use Phile\Gateway\EventObserverInterface;

/**
 * the Event class for implementing a hook/event system
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class Event
{
    /**
     * Registry object provides storage for objects.
     *
     * @var array
     */
    protected static $registry = [];

    /**
     * method to register an event
     *
     * @param string                          $eventName the event to observe
     * @param EventObserverInterface|callable $object    observer
     */
    public static function registerEvent($eventName, $object)
    {
        if ($object instanceof EventObserverInterface) {
            $object = [$object, 'on'];
        }
        if (!is_callable($object)) {
            throw new \InvalidArgumentException(
                "Can't register event. Observer is not callable.",
                1427814905
            );
        }
        self::$registry[$eventName][] = $object;
    }

    /**
     * method to trigger an event
     *
     * @param string $eventName the event name (register for this name)
     * @param array  $data      array with some additional data
     */
    public static function triggerEvent($eventName, $data = null)
    {
        if (empty(self::$registry[$eventName])) {
            return;
        }
        foreach (self::$registry[$eventName] as $observer) {
            call_user_func_array($observer, [$eventName, $data]);
        }
    }
}
