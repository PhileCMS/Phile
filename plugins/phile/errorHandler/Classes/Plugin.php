<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\ErrorHandler;

/**
 * Class Plugin
 * Default Phile parser plugin for Markdown
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMarkdown
 */
class Plugin extends \Phile\Plugin\AbstractPlugin {
	const HANDLER_ERROR_LOG		= 'error_log';
	const HANDLER_DEVELOPMENT	= 'development';

	protected $events = ['plugins_loaded' => 'onPluginsLoaded'];

	/**
	 * called on 'plugins_loaded' event
	 *
	 * @param null $data
	 * @throws \Phile\Exception\ServiceLocatorException
	 */
	public function onPluginsLoaded($data = null) {
		switch ($this->settings['handler']) {
			case Plugin::HANDLER_ERROR_LOG:
				\Phile\ServiceLocator::registerService('Phile_ErrorHandler',
					new \Phile\Plugin\Phile\ErrorHandler\ErrorLog($this->settings));
				break;
			case Plugin::HANDLER_DEVELOPMENT:
				\Phile\ServiceLocator::registerService('Phile_ErrorHandler',
					new \Phile\Plugin\Phile\ErrorHandler\Development($this->settings));
				break;
		}
	}
}
