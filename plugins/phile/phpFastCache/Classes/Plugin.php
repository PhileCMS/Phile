<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\PhpFastCache;

/**
 * Class Plugin
 * Default Phile cache engine
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\PhpFastCache
 */
class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	/**
	 * the constructor
	 */
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	/**
	 * event method
	 *
	 * @param string $eventKey
	 * @param null   $data
	 *
	 * @return mixed|void
	 */
	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			// phpFastCache not working in CLI mode...
			if (!PHILE_CLI_MODE) {
				require_once(\Phile\Utility::resolveFilePath('MOD:phile/phpFastCache/lib/phpfastcache/phpfastcache.php'));

				\phpFastCache::setup($this->settings);

				try {
					// try to instantiate a new cache
					$cache = phpFastCache();
				} catch (\Exception $e) {
					if ($e->getCode()==100) {
						// An exception with error code 100 means that the cache directory is not
						// writable or does not exist. Usually this would mean that this is the
						// first time that the cache is actually switched on. We try to create
						// the cache directory with the proper access rights and try again.
						if (!file_exists($this->settings['path'])) {
							@mkdir($this->settings['path'], 0755);
						}

						// Try again. If there is still an error at this point, then show the
						// exception (we don't catch it)
						$cache = phpFastCache();
					}
				}
				\Phile\ServiceLocator::registerService('Phile_Cache', new \Phile\Plugin\Phile\PhpFastCache\PhpFastCache($cache));
			}
		}
	}
}
