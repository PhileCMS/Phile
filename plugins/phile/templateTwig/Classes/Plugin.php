<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\TemplateTwig;

/**
 * Class Plugin
 * Default Phile template engine
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TemplateTwig
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
		\Phile\ServiceLocator::registerService('Phile_Template',
			new \Phile\Plugin\Phile\TemplateTwig\Template\Twig($this->settings));
	}
}
