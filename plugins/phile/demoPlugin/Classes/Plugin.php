<?php
/**
 * Plugin class
 */
namespace Phile\Plugin\Phile\DemoPlugin;

/**
 * the class name is allways Plugin
 * the namespace starts with Phile\Plugin\
 * the vendor name in lowercase is the folder name under plugins directory
 * the subfolder with lowerCamelCase is the plugin name
 *
 * your namespace should be: Phile\Plugin\Mycompany\MyPluginName
 * your plugin folder should be: plugins/mycompany/myPluginName/
 *
 * Class Plugin
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\DemoPlugin
 */
class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	// $this->settings will be filled with the data from the config.php file from the plugin folder
	// var_dump($this->settings);
	/**
	 * the contructor
	 */
	public function __construct() {
		\Phile\Core\Event::registerEvent('before_render_template', $this);
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
		var_dump(array($eventKey, $data));
	}
}