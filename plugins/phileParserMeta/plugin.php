<?php

/**
 * Default Phile parser plugin for Markdown
 */
class PhileParserMeta extends \Phile\Plugin\AbstractPlugin implements \Phile\EventObserverInterface {
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			\Phile\ServiceLocator::registerService('Phile_Parser_Meta', new \Phile\Parser\Meta($this->settings));
		}
	}
}
