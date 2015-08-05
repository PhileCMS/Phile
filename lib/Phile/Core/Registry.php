<?php
/**
 * Registry class
 */
namespace Phile\Core;

/**
 * the Registry class for implementing a registry
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class Registry extends \ArrayObject
{
    /**
     * Registry object provides storage for shared objects.
     *
     * @var Registry
     */
    private static $registry = null;

    /**
     * Retrieves the default registry instance.
     *
     * @return Registry
     */
    public static function getInstance()
    {
        if (self::$registry === null) {
            self::init();
        }

        return self::$registry;
    }

    /**
     * Set the default registry instance to a specified instance.
     *
     * @param Registry $registry An object instance of type Registry,
     *                           or a subclass.
     *
     * @param Registry $registry
     *
     * @throws \Exception
     */
    public static function setInstance(Registry $registry)
    {
        if (self::$registry !== null) {
            throw new \Exception('Registry is already initialized', 1398536572);
        }
        self::$registry = $registry;
    }

    /**
     * Initialize the default registry instance.
     *
     * @return void
     */
    protected static function init()
    {
        self::setInstance(new Registry());
    }

    /**
     * getter method, basically same as offsetGet().
     *
     * This method can be called from an object of type Registry, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index - get the value associated with $index
     *
     * @return mixed
     * @throws \Exception if no entry is registerd for $index.
     */
    public static function get($index)
    {
        $instance = self::getInstance();
        if (!$instance->offsetExists($index)) {
            throw new \Exception("No entry is registered for key '$index'", 1398536594);
        }

        return $instance->offsetGet($index);
    }

    /**
     * setter method, basically same as offsetSet().
     *
     * This method can be called from an object of type Registry, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index The location in the ArrayObject in which to store
     *                      the value.
     * @param mixed  $value The object to store in the ArrayObject.
     *
     * @return void
     */
    public static function set($index, $value)
    {
        $instance = self::getInstance();
        $instance->offsetSet($index, $value);
    }

    /**
     * Returns TRUE if the $index is a named value in the registry,
     * or FALSE if $index was not found in the registry.
     *
     * @param string $index
     *
     * @return boolean
     */
    public static function isRegistered($index)
    {
        if (self::$registry === null) {
            return false;
        }

        return self::$registry->offsetExists($index);
    }

    /**
     * the constructor
     *
     * @param array   $array data array
     * @param integer $flags ArrayObject flags
     */
    public function __construct($array = array(), $flags = parent::ARRAY_AS_PROPS)
    {
        parent::__construct($array, $flags);
    }

    /**
     * method to check if offset exists
     *
     * @param string $index
     *
     * @returns mixed
     *
     * Workaround for http://bugs.php.net/bug.php?id=40442 (ZF-960).
     */
    public function offsetExists($index)
    {
        return array_key_exists($index, $this);
    }
}
