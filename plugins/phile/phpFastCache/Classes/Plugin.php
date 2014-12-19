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
		\Phile\Core\Event::registerEvent('plugins_loaded', $this);
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
				require_once(\Phile\Core\Utility::resolveFilePath('MOD:phile/phpFastCache/lib/phpfastcache/phpfastcache.php'));

				\phpFastCache::setup($this->settings);
				$cache = phpFastCache();
				\Phile\Core\ServiceLocator::registerService('Phile_Cache', new \Phile\Plugin\Phile\PhpFastCache\PhpFastCache($cache));
			}
		}
	}
}
