<?php

namespace Phile\Plugin;
use Phile\Registry;

/**
 * the AbstractPlugin class for implementing a plugin for PhileCMS
 * @author Frank Nägler
 */
abstract class AbstractPlugin {
	/**
	 * @var array the plugin settings
	 */
	protected $settings;

	public function __construct() {
		$settings   = Registry::get('Phile_Settings');
		$pluginKey  = lcfirst(get_class($this));
		$this->settings = $settings['plugins'][$pluginKey]['settings'];
	}
}
