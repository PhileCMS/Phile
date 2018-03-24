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
class Plugin extends AbstractPlugin
{

    protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

    /**
     * onPluginsLoaded method
     */
    public function onPluginsLoaded()
    {
        $storage = $this->settings['storage'];
        $config = $this->settings;
        unset($config['active'], $config['storage']);
        $psr16Cache = new \phpFastCache\Helper\Psr16Adapter($storage, $config);

        $phileCache = new PhileToPsr16CacheAdapter($psr16Cache);
        ServiceLocator::registerService('Phile_Cache', $phileCache);
    }
}
