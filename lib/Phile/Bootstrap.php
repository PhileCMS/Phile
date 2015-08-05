<?php
/**
 * the Bootstrap of Phile
 */
namespace Phile;

use Phile\Exception\PluginException;
use Phile\Plugin\PluginRepository;

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
		$this->initializeFilesAndFolders();
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
		defined('PHILE_VERSION') 	or define('PHILE_VERSION',   '1.6.0');
		defined('PHILE_CLI_MODE') 	or define('PHILE_CLI_MODE',  (php_sapi_name() == "cli") ? true : false);
		defined('ROOT_DIR') 		or define('ROOT_DIR',        realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
		defined('CONTENT_DIR') 		or define('CONTENT_DIR',     ROOT_DIR . 'content' . DIRECTORY_SEPARATOR);
		defined('CONTENT_EXT') 		or define('CONTENT_EXT',     '.md');
		defined('LIB_DIR') 			or define('LIB_DIR',         ROOT_DIR . 'lib' . DIRECTORY_SEPARATOR);
		defined('PLUGINS_DIR') 		or define('PLUGINS_DIR',     ROOT_DIR . 'plugins' . DIRECTORY_SEPARATOR);
		defined('THEMES_DIR') 		or define('THEMES_DIR',      ROOT_DIR . 'themes' . DIRECTORY_SEPARATOR);
		defined('CACHE_DIR') 		or define('CACHE_DIR',       LIB_DIR . 'cache' . DIRECTORY_SEPARATOR);
		defined('STORAGE_DIR') or define('STORAGE_DIR', LIB_DIR . 'datastorage' . DIRECTORY_SEPARATOR);
	}

	/**
	 * initialize the autoloader
	 */
	protected function initializeAutoloader() {
		spl_autoload_extensions(".php");
		// load phile core
		spl_autoload_register(function ($className) {
			$fileName = LIB_DIR . str_replace("\\", DIRECTORY_SEPARATOR, $className) . '.php';
			if (file_exists($fileName)) {
				require_once $fileName;
			}
		});
		// load phile plugins
		spl_autoload_register('\Phile\Plugin\PluginRepository::autoload');

		require(LIB_DIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
	}

	/**
	 * initialize configuration
	 */
	protected function initializeConfiguration() {
		$defaults      = Utility::load(ROOT_DIR . 'default_config.php');
		$localSettings = Utility::load(ROOT_DIR . 'config.php');
		if (is_array($localSettings)) {
			$this->settings = array_replace_recursive($defaults, $localSettings);
		} else {
			$this->settings = $defaults;
		}

		Registry::set('Phile_Settings', $this->settings);
		date_default_timezone_set($this->settings['timezone']);
	}

	/**
	 * auto-setup of files and folders
	 */
	protected function initializeFilesAndFolders() {
		$dirs = [
			['path' => CACHE_DIR],
			['path' => STORAGE_DIR]
		];
		$defaults = ['protected' => true];

		foreach ($dirs as $dir) {
			$dir += $defaults;
			$path = $dir['path'];
			if (empty($path) || strpos($path, ROOT_DIR) !== 0) {
				continue;
			}
			if (!file_exists($path)) {
				mkdir($path, 0775, true);
			}
			if ($dir['protected']) {
				$file = "$path.htaccess";
				if (!file_exists($file)) {
					$content = "order deny,allow\ndeny from all\nallow from 127.0.0.1";
					file_put_contents($file, $content);
				}
			}
		}
	}

	/**
	 * initialize plugins
	 *
	 * @throws Exception\PluginException
	 */
	protected function initializePlugins() {
		$loader = new PluginRepository();
		if (isset($this->settings['plugins']) && is_array($this->settings['plugins'])) {
			$this->plugins = $loader->loadAll($this->settings['plugins']);
		}

		Event::triggerEvent('plugins_loaded', ['plugins' => $this->plugins]);

		// throw not earlier to have the error-handler plugin loaded
		// and initialized (by 'plugins_loaded' event)
		$errors = $loader->getLoadErrors();
		if (count($errors) > 0) {
			throw new PluginException($errors[0]['message'], $errors[0]['code']);
		}

		// settings now include initialized plugin-configs
		$this->settings = Registry::get('Phile_Settings');
		Event::triggerEvent('config_loaded', ['config' => $this->settings]);
	}

	/**
	 * method to get plugins
	 * @return array
	 * @deprecated since 1.5 will be removed
	 * @use 'plugins_loaded' event
	 */
	public function getPlugins() {
		return $this->plugins;
	}
}
