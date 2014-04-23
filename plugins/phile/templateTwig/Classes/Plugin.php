<?php

namespace Phile\Plugin\Phile\TemplateTwig;

/**
 * Class Plugin
 * Default Phile template engine
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TemplateTwig
 */
class Plugin extends \Phile\Plugin\AbstractPlugin implements \Phile\Gateway\EventObserverInterface {
	public function __construct() {
		\Phile\Event::registerEvent('plugins_loaded', $this);
	}

	public function on($eventKey, $data = null) {
		// check $eventKey for which you have registered
		if ($eventKey == 'plugins_loaded') {
			\Phile\ServiceLocator::registerService('Phile_Template', new \Phile\Plugin\Phile\TemplateTwig\Template\Twig($this->settings));
		}
	}
}
