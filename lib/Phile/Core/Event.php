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
     * @var Event global instance
     * @deprecated static use is deprecated
     */
    protected static $instance;

    /**
     * Registry object provides storage for objects.
     *
     * @var array
     */
    protected $registry = [];

    /**
     * get global event instance
     *
     * @return Event
     * @deprecated static use is deprectated
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Set global event instance
     *
     * @param Event $instance
     * @deprecated static use is deprecated
     */
    public static function setInstance(Event $instance)
    {
        static::$instance = $instance;
    }

    /**
     * Global register
     *
     * @param string $eventName
     * @param EventObserverInterface|callable $object observer
     * @deprecated static use is deprecated
     */
    public static function registerEvent($eventName, $object)
    {
        static::$instance->register($eventName, $object);
    }

    /**
     * Global trigger
     *
     * @param string $eventName
     * @param array $data
     * @deprecated static use is deprecated
     */
    public static function triggerEvent($eventName, $data = null)
    {
        static::$instance->trigger($eventName, $data);
    }

    /**
     * method to register an event
     *
     * @param string $eventName the event to observe
     * @param EventObserverInterface|callable $object observer
     */
    public function register(string $eventName, $object): void
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
        $this->registry[$eventName][] = $object;
    }

    /**
     * method to trigger an event
     *
     * @param string $eventName the event name (register for this name)
     * @param array $data array with some additional data
     */
    public function trigger(string $eventName, array $data = null): void
    {
        if (empty($this->registry[$eventName])) {
            return;
        }
        foreach ($this->registry[$eventName] as $observer) {
            call_user_func_array($observer, [$eventName, $data]);
        }
    }
}
