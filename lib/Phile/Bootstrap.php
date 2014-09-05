<?php
/**
 * the Bootstrap of Phile
 */
namespace Phile;
use Phile\Plugin\AbstractPlugin;
use Phile\Exception\PluginException;

/**
 * Phile
 *
 * @author Frank Nägler
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */
class Bootstrap {
	/**
	 * @var \Phile\Bootstrap instance of Bootstrap class
	 */
	static protected $instance = NULL;

	/**
	 * @var array the settings array
	 */
	protected $settings;

	/**
	 * @var array the loaded plugins
	 */
	protected $plugins;

	/**
	 * the constructor
	 * Disable direct creation of this object.
	 */
	protected function __construct() {
	}

	/**
	 * Disable direct cloning of this object.
	 */
	protected function __clone() {
	}

	/**
	 * Return instance of Bootstrap class as singleton
	 *
	 * @return Bootstrap
	 */
	static public function getInstance() {
		if (is_null(static::$instance)) {
			self::$instance = new Bootstrap();
		}
		return static::$instance;
	}

	/**
	 * initialize basics
	 */
	public function initializeBasics() {
		$this->initializeDefinitions();
		$this->initializeAutoloader();
		$this->initializeConfiguration();
		$this->initializePlugins();
		return $this;
	}

	/**
	 * initialize the global definitions
	 */
	protected function initializeDefinitions() {
		// for php unit testings, we need to check if constant is defined
		// before setting them, because there is a bug in PHPUnit which
		// init our bootstrap multiple times.
		defined('PHILE_VERSION') 	or define('PHILE_VERSION',   '1.3.0');
		defined('PHILE_CLI_MODE') 	or define('PHILE_CLI_MODE',  (php_sapi_name() == "cli") ? true : false);
		defined('ROOT_DIR') 		or define('ROOT_DIR',        realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
		defined('CONTENT_DIR') 		or define('CONTENT_DIR',     ROOT_DIR . 'content' . DIRECTORY_SEPARATOR);
		defined('CONTENT_EXT') 		or define('CONTENT_EXT',     '.md');
		defined('LIB_DIR') 			or define('LIB_DIR',         ROOT_DIR . 'lib' . DIRECTORY_SEPARATOR);
		defined('PLUGINS_DIR') 		or define('PLUGINS_DIR',     ROOT_DIR . 'plugins' . DIRECTORY_SEPARATOR);
		defined('THEMES_DIR') 		or define('THEMES_DIR',      ROOT_DIR . 'themes' . DIRECTORY_SEPARATOR);
		defined('CACHE_DIR') 		or define('CACHE_DIR',       LIB_DIR . 'cache' . DIRECTORY_SEPARATOR);
	}

	/**
	 * initialize the autoloader
	 */
	protected function initializeAutoloader() {
		spl_autoload_extensions(".php");
		spl_autoload_register(function ($className) {
			$fileName = LIB_DIR . str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php';
			if (file_exists($fileName)) {
				require_once $fileName;
			} else {
				// autoload plugin namespace
				if (strpos($className, "Phile\\Plugin\\") === 0) {
					$className 		= substr($className, 13);
					$classNameParts = explode('\\', $className);
					$pluginVendor 	= lcfirst(array_shift($classNameParts));
					$pluginName 	= lcfirst(array_shift($classNameParts));
					$classPath		= array_merge(array($pluginVendor, $pluginName, 'Classes'), $classNameParts);
					$fileName 		= PLUGINS_DIR . implode(DIRECTORY_SEPARATOR, $classPath) . '.php';
					if (file_exists($fileName)) {
						require_once $fileName;
					}
				}
			}
		});

		require(LIB_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
	}

	/**
	 * initialize configuration
	 */
	protected function initializeConfiguration() {
		$defaults      = Utility::load(ROOT_DIR . '/default_config.php');
		$localSettings = Utility::load(ROOT_DIR . '/config.php');
		if (is_array($localSettings)) {
			$this->settings = array_replace_recursive($defaults, $localSettings);
		} else {
			$this->settings = $defaults;
		}

		\Phile\Registry::set('Phile_Settings', $this->settings);
		date_default_timezone_set($this->settings['timezone']);
	}

	/**
	 * initialize plugins
	 *
	 * @throws Exception
	 */
	protected function initializePlugins() {
		$loadingErrors = array();
		// check to see if there are plugins to be loaded
		if (isset($this->settings['plugins']) && is_array($this->settings['plugins'])) {
			foreach ($this->settings['plugins'] as $pluginKey => $pluginConfig) {
				list($vendor, $pluginName) = explode('\\', $pluginKey);

				if (isset($pluginConfig['active']) && $pluginConfig['active'] === true) {
					// load plugin configuration...
					$pluginConfiguration = null;
					// load the config file for the plugin
					$configFile = \Phile\Utility::resolveFilePath("MOD:" . $vendor . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR . "config.php");
					if ($configFile !== null) {
						$pluginConfiguration = \Phile\Utility::load($configFile);
						$globalConfiguration = \Phile\Registry::get('Phile_Settings');
						if ($pluginConfiguration !== null && is_array($pluginConfiguration)) {
							$globalConfiguration['plugins'][$pluginKey]['settings'] = array_replace_recursive($pluginConfiguration, $globalConfiguration['plugins'][$pluginKey]);
						} else {
							$globalConfiguration['plugins'][$pluginKey]['settings'] = array();
						}
						\Phile\Registry::set('Phile_Settings', $globalConfiguration);
						$this->settings = $globalConfiguration;
					}
					// uppercase first letter convention
					$pluginClassName = '\\Phile\\Plugin\\' . ucfirst($vendor) . '\\' . ucfirst($pluginName) . '\\Plugin';
					if (!class_exists($pluginClassName)) {
						$loadingErrors[] = array("the plugin '{$pluginKey}' could not be loaded!", 1398536479);
						continue;
					}

					/** @var \Phile\Plugin\AbstractPlugin $plugin */
					$plugin = new $pluginClassName;
					$plugin->injectSettings($globalConfiguration['plugins'][$pluginKey]['settings']);

					if ($plugin instanceof \Phile\Plugin\AbstractPlugin) {
						// register plugin
						$this->plugins[$pluginKey] = $plugin;
					} else {
						$loadingErrors[] = array("the plugin '{$pluginKey}' is not an instance of \\Phile\\Plugin\\AbstractPlugin", 1398536526);
						continue;
					}
				}
			}
		}
		/**
		 * @triggerEvent plugins_loaded this event is triggered after the plugins loaded
		 * This is also where we load the parser, since it is a plugin also. We use the Markdown parser as default. See it in the plugins folder and lib/Phile/Parser/Markdown.php
		 */
		Event::triggerEvent('plugins_loaded');

		if (count($loadingErrors) > 0) {
			throw new PluginException($loadingErrors[0][0], $loadingErrors[0][1]);
		}

		/**
		 * @triggerEvent config_loaded this event is triggered after the configuration is fully loaded
		 */
		Event::triggerEvent('config_loaded');
	}

	/**
	 * method to get plugins
	 * @return array
	 */
	public function getPlugins() {
		return $this->plugins;
	}
}
