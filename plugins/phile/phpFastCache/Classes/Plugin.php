<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\PhpFastCache;

use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;
use Phpfastcache\Config\ConfigurationOption;
use Phpfastcache\Helper\Psr16Adapter;

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
        unset($config['active'], $config['storage']);
        $psr16Cache = new Psr16Adapter($storage, new ConfigurationOption($config));

        $phileCache = new PhileToPsr16CacheAdapter($psr16Cache);
        ServiceLocator::registerService('Phile_Cache', $phileCache);
    }
}
