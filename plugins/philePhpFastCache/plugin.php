<?php

/**
 * Default Phile cache engine
 */
class PhilePhpFastCache extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			require_once(\Phile\Utility::resolveFilePath('MOD:philePhpFastCache/lib/PhpFastCache.php'));
			require_once(\Phile\Utility::resolveFilePath('MOD:philePhpFastCache/lib/phpfastcache/phpfastcache.php'));

			phpFastCache::setup($this->settings);
			$cache = phpFastCache();
			\Phile\ServiceLocator::registerService('Phile_Cache', new \Phile\Cache\PhpFastCache($cache));
		}
	}
}
