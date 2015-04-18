<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\ErrorHandler;

use Phile\Core\Router;
use Phile\Core\ServiceLocator;
use Phile\Plugin\AbstractPlugin;
use Phile\Plugin\Phile\ErrorHandler\Development;
use Phile\Plugin\Phile\ErrorHandler\ErrorLog;

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
		$this->settings['base_url'] = (new Router)->getBaseUrl();
		switch ($this->settings['handler']) {
			case Plugin::HANDLER_ERROR_LOG:
				ServiceLocator::registerService('Phile_ErrorHandler',
					new ErrorLog($this->settings));
				break;
			case Plugin::HANDLER_DEVELOPMENT:
				ServiceLocator::registerService('Phile_ErrorHandler',
					new Development($this->settings));
				break;
		}
	}
}
