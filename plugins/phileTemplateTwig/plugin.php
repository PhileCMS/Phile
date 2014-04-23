<?php

/**
 * Default Phile template engine
 */
class PhileTemplateTwig extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			\Phile\ServiceLocator::registerService('Phile_Template', new \Phile\Plugin\PhileTemplateTwig\Template\Twig($this->settings));
		}
	}
}
