<?php

/**
 * Class PhileDemoPlugin <= the class name is the pluginKey but first char uppercase!
 * important: the pluginKey is also the folder name!
 */
class PhileDemoPlugin extends \Phile\Plugin\AbstractPlugin implements \Phile\EventObserverInterface {
	// $this->settings will be filled with the data from the config.php file from the plugin folder
	// var_dump($this->settings);
	public function __construct() {
		// \Phile\Event::registerEvent('test1', $this);
		// \Phile\Event::registerEvent('test2', $this);
	}

	public function on($eventKey, $data) {
		// check $eventKey for which you have registered
		// var_dump(array($eventKey, $data));
	}
}