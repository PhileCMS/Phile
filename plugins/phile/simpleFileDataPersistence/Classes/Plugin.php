<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\SimpleFileDataPersistence;

/**
 * Class Plugin
 * Default Phile data persistence engine
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\SimpleFileDataPersistence
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
		\Phile\ServiceLocator::registerService('Phile_Data_Persistence',
			new \Phile\Plugin\Phile\SimpleFileDataPersistence\Persistence\SimpleFileDataPersistence());
	}
}
