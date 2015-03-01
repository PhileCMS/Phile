<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\PhpFastCache;

/**
 * Class Plugin
 * Default Phile cache engine
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\PhpFastCache
 */
class Plugin extends \Phile\Plugin\AbstractPlugin {

	protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

	/**
	 * onPluginsLoaded method
	 *
	 * @param null   $data
	 *
	 * @return mixed|void
	 */
	public function onPluginsLoaded($data = null) {
		// phpFastCache not working in CLI mode...
		if (!PHILE_CLI_MODE) {
			require_once(\Phile\Utility::resolveFilePath('MOD:phile/phpFastCache/lib/phpfastcache/phpfastcache.php'));

			\phpFastCache::setup($this->settings);
			$cache = phpFastCache();
			\Phile\ServiceLocator::registerService('Phile_Cache',
				new \Phile\Plugin\Phile\PhpFastCache\PhpFastCache($cache));
		}
	}
}
