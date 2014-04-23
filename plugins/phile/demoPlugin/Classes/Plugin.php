<?php

namespace Phile\Plugin\Phile\DemoPlugin;

/**
 * Class PhileDemoPlugin <= the class name is the pluginKey but first char uppercase!
 * important: the pluginKey is also the folder name!
 */
class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	// $this->settings will be filled with the data from the config.php file from the plugin folder
	// var_dump($this->settings);
	public function __construct() {
		\Phile\Event::registerEvent('before_render_template', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		var_dump(array($eventKey, $data));
	}
}