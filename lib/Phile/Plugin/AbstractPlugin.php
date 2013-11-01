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

	/**
	 * inject settings
	 * @param array $settings
	 */
	public function injectSettings(array $settings = null) {
		$this->settings = ($settings === null) ? array() : $settings;
	}
}
