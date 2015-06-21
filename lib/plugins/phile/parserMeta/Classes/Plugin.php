<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\ParserMeta;

use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\Phile\ParserMeta\Parser\Meta;

/**
 * Class Plugin
 * Default Phile parser plugin for Markdown
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMeta
 */
class Plugin extends AbstractPlugin {

	protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

	/**
	 * onPluginsLoaded method
	 *
	 * @param null   $data
	 *
	 * @return mixed|void
	 */
	public function onPluginsLoaded($data = null) {
		ServiceLocator::registerService('Phile_Parser_Meta',
			new Meta($this->settings));
	}

}
