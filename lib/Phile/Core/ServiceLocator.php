<?php
/*
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Core;

use Phile\Core\Container;
use Phile\Exception\ServiceLocatorException;

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
     *
     * @throws ServiceLocatorException
     */
    public static function registerService($serviceKey, $object)
    {
        Container::getInstance()->set($serviceKey, $object);
    }

    /**
     * checks if a service is registered
     *
     * @param string $serviceKey
     *
     * @return bool
     */
    public static function hasService($serviceKey)
    {
        return Container::getInstance()->has($serviceKey);
    }

    /**
     * returns a service
     *
     * @param string $serviceKey the service key
     *
     * @return mixed
     * @throws ServiceLocatorException
     */
    public static function getService($serviceKey)
    {
        return Container::getInstance()->get($serviceKey);
    }
}
