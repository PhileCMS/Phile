<?php

namespace Phile\Plugin\Phile\ParserMeta;

/**
 * Class Plugin
 * Default Phile parser plugin for Markdown
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMeta
 */
class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			\Phile\ServiceLocator::registerService('Phile_Parser_Meta', new \Phile\Plugin\Phile\ParserMeta\Parser\Meta($this->settings));
		}
	}
}
