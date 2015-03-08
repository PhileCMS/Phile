<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\ParserMeta;

/**
 * Class Plugin
 * Default Phile parser plugin for Markdown
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMeta
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
		\Phile\ServiceLocator::registerService('Phile_Parser_Meta',
			new \Phile\Plugin\Phile\ParserMeta\Parser\Meta($this->settings));
	}

}
