<?php
/*
 * @author  PhileCMS
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Core;

use Phile\Core\Container;

/**
 * the Service Locator class
 */
class ServiceLocator
{
    /**
     * method to register a service
     *
     * @param string $serviceKey the key for the service
     * @param mixed  $object
     * @return void
     */
    public static function registerService(string $serviceKey, $object): void
    {
        Container::getInstance()->set($serviceKey, $object);
    }

    /**
     * checks if a service is registered
     *
     * @param string $serviceKey
     * @return bool
     */
    public static function hasService(string $serviceKey)
    {
        return Container::getInstance()->has($serviceKey);
    }

    /**
     * returns a service
     *
     * @param string $serviceKey the service key
     * @return mixed
     */
    public static function getService(string $serviceKey)
    {
        return Container::getInstance()->get($serviceKey);
    }
}
