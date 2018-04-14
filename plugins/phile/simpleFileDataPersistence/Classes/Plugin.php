<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\SimpleFileDataPersistence;

use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\Phile\SimpleFileDataPersistence\Persistence\SimpleFileDataPersistence;

/**
 * Class Plugin
 * Default Phile data persistence engine
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\SimpleFileDataPersistence
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
     * @param array $data
     * @return void
     */
    public function onPluginsLoaded($data)
    {
        ServiceLocator::registerService(
            'Phile_Data_Persistence',
            new SimpleFileDataPersistence($this->settings['storage_dir'])
        );
    }
}
