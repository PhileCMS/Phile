<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\PhpFastCache;

use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;

/**
 * Class Plugin
 * Default Phile cache engine
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\PhpFastCache
 */
class Plugin extends AbstractPlugin {

	protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

	/**
	 * onPluginsLoaded method
	 */
	public function onPluginsLoaded() {
		// phpFastCache not working in CLI mode...
		if (PHILE_CLI_MODE) {
			return;
		}
		unset($this->settings['active']);
		$config = $this->settings + \phpFastCache::$config;
		$storage = $this->settings['storage'];
		$cache = phpFastCache($storage, $config);
		ServiceLocator::registerService('Phile_Cache', new PhpFastCache($cache));
	}
}
