<?php
/**
 * The SerciveLocator class
 */
namespace Phile\Core;
use Phile\Exception\ServiceLocatorException;

/**
 * the Service Locator class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class ServiceLocator {
	/**
	 * @var array of services
	 */
	protected static $services;

	/**
	 * @var array $serviceMap for mapping speaking names/keys to the interfaces
	 */
	protected static $serviceMap = array(
		'Phile_Cache'            => 'Phile\ServiceLocator\CacheInterface',
		'Phile_Template'         => 'Phile\ServiceLocator\TemplateInterface',
		'Phile_Parser'           => 'Phile\ServiceLocator\ParserInterface',
		'Phile_Data_Persistence' => 'Phile\ServiceLocator\PersistenceInterface',
		'Phile_Parser_Meta'      => 'Phile\ServiceLocator\MetaInterface',
		'Phile_ErrorHandler'	 => 'Phile\ServiceLocator\ErrorHandlerInterface',
	);

	/**
	 * method to register a service
	 *
	 * @param string $serviceKey the key for the service
	 * @param mixed  $object
	 *
	 * @throws ServiceLocatorException
	 */
	public static function registerService($serviceKey, $object) {
		$interfaces = class_implements($object);
		$interface  = self::$serviceMap[$serviceKey];
		if ($interfaces === false || !in_array($interface, $interfaces)) {
			throw new ServiceLocatorException("the object must implement the interface: '{$interface}'", 1398536617);
		}
		self::$services[$serviceKey] = $object;
	}

	/**
	 * checks if a service is registered
	 *
	 * @param string $serviceKey
	 *
	 * @return bool
	 */
	public static function hasService($serviceKey) {
		return (isset(self::$services[$serviceKey]));
	}

	/**
	 * returns a service
	 *
	 * @param string $serviceKey the service key
	 *
	 * @return mixed
	 * @throws ServiceLocatorException
	 */
	public static function getService($serviceKey) {
		if (!isset(self::$services[$serviceKey])) {
			throw new ServiceLocatorException("the service '{$serviceKey}' is not registered", 1398536637);
		}

		return self::$services[$serviceKey];
	}
}
