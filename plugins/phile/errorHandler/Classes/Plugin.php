<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\ErrorHandler;

/**
 * Class Plugin
 * Default Phile parser plugin for Markdown
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMarkdown
 */
class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	const HANDLER_ERROR_LOG		= 'error_log';
	const HANDLER_DEVELOPMENT	= 'development';

	/**
	 * the constructor
	 */
	public function __construct() {
		\Phile\Core\Event::registerEvent('plugins_loaded', $this);
	}

	/**
	 * event method
	 *
	 * @param string $eventKey
	 * @param null   $data
	 *
	 * @return mixed|void
	 */
	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			switch ($this->settings['handler']) {
				case Plugin::HANDLER_ERROR_LOG:
					\Phile\Core\ServiceLocator::registerService('Phile_ErrorHandler', new \Phile\Plugin\Phile\ErrorHandler\ErrorLog($this->settings));
				break;
				case Plugin::HANDLER_DEVELOPMENT:
					\Phile\Core\ServiceLocator::registerService('Phile_ErrorHandler', new \Phile\Plugin\Phile\ErrorHandler\Development($this->settings));
				break;
			}
		}
	}
}
