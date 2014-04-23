<?php

namespace Phile\Plugin\Phile\SimpleFileDataPersistence;

/**
 * Default Phile data persistence engine
 */
class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			\Phile\ServiceLocator::registerService('Phile_Data_Persistence', new \Phile\Plugin\Phile\SimpleFileDataPersistence\Persistence\SimpleFileDataPersistence());
		}
	}
}
