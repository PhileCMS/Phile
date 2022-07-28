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
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\PhpFastCache
 */
class Plugin extends AbstractPlugin
{
    /**
     * {@inheritDoc}
     */
    protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

    /**
     * onPluginsLoaded method
     *
     * @return void
     */
    public function onPluginsLoaded()
    {
        $storage = $this->settings['storage'];
        $config = $this->settings;

        if (!empty($config['phpFastCacheConfig'])) {
            $cacheConfig = $config['phpFastCacheConfig'];
        } else {
            // Remove options unknown to Phpfastcache triggering errors
            unset($config['active'], $config['storage']);
            if ($storage == 'files') {
                $cacheConfig = new \Phpfastcache\Drivers\Files\Config($config);
            } else {
                $cacheConfig = new \Phpfastcache\Config\ConfigurationOption($config);
            }
        }

        $psr16Cache = new \Phpfastcache\Helper\Psr16Adapter($storage, $cacheConfig);
        $phileCache = new PhileToPsr16CacheAdapter($psr16Cache);
        ServiceLocator::registerService('Phile_Cache', $phileCache);
    }
}
