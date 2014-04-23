<?php

/**
 * Default Phile parser plugin for Markdown
 */
class PhileParserMarkdown extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			\Phile\ServiceLocator::registerService('Phile_Parser', new \Phile\Plugin\PhileParserMarkdown\Parser\Markdown($this->settings));
		}
	}
}
