<?php

namespace Phile\Plugin;

use Phile\Exception\PluginException;

/**
 * Class PluginRepository manages plugin loading
 *
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Core
 */
class PluginRepository {

	/** @var array of AbstractPlugin */
	protected $plugins = [];

	/** @var array errors during load; keys: 'message' and 'code' */
	protected $loadErrors = [];

	/**
	 * get load errors
	 *
	 * @return array
	 */
	public function getLoadErrors() {
		return $this->loadErrors;
	}

	/**
	 * loads all activated plugins from $settings
	 *
	 * @param array $settings plugin-settings
	 * @return array of AbstractPlugin
	 * @throws PluginException
	 */
	public function loadAll($settings) {
		$this->reset();
		foreach ($settings as $pluginKey => $config) {
			if (!isset($config['active']) || !$config['active']) {
				continue;
			}
			try {
				$this->plugins[$pluginKey] = $this->load($pluginKey);
			} catch (PluginException $e) {
				$this->loadErrors[] = [
					'message' => $e->getMessage(),
					'code' => $e->getCode()
				];
			}
		}
		return $this->plugins;
	}

	/**
	 * load and return single plugin
	 *
	 * @param $pluginKey
	 * @return AbstractPlugin
	 * @throws PluginException
	 */
	protected function load($pluginKey) {
		list($vendor, $pluginName) = explode('\\', $pluginKey);
		// uppercase first letter convention
		$pluginClassName = '\\Phile\\Plugin\\' . ucfirst($vendor) . '\\' . ucfirst($pluginName) . '\\Plugin';

		if (!class_exists($pluginClassName)) {
			throw new PluginException(
				"the plugin '{$pluginKey}' could not be loaded!",
				1398536479
			);
		}

		/** @var \Phile\Plugin\AbstractPlugin $plugin */
		$plugin = new $pluginClassName;
		if (($plugin instanceof AbstractPlugin) === false) {
			throw new PluginException(
				"the plugin '{$pluginKey}' is not an instance of \\Phile\\Plugin\\AbstractPlugin",
				1398536526
			);
		}

		$plugin->initializePlugin($pluginKey);
		return $plugin;
	}

	/**
	 * clear out repository
	 */
	protected function reset() {
		$this->loadErrors = [];
		$this->plugins = [];
	}

	/**
	 * auto-loader plugin namespace
	 *
	 * @param $className
	 */
	public static function autoload($className) {
		if (strpos($className, "Phile\\Plugin\\") !== 0) {
			return;
		}

		$className = substr($className, 13);
		$classNameParts = explode('\\', $className);
		$pluginVendor = lcfirst(array_shift($classNameParts));
		$pluginName = lcfirst(array_shift($classNameParts));
		$classPath = array_merge(
			[$pluginVendor, $pluginName, 'Classes'],
			$classNameParts
		);

		$fileName = PLUGINS_DIR . implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
		if (file_exists($fileName)) {
			require_once $fileName;
		}
	}

}
