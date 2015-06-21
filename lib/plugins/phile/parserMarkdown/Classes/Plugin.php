<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\ParserMarkdown;

use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\Phile\ParserMarkdown\Parser\Markdown;

/**
 * Class Plugin
 * Default Phile parser plugin for Markdown
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMarkdown
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
		ServiceLocator::registerService('Phile_Parser', new Markdown($this->settings));
	}
}
