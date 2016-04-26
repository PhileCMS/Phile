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
 * @author  Frank Nägler
 * @link    https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */
class Bootstrap
{
    /**
     * @var \Phile\Bootstrap instance of Bootstrap class
     */
    static protected $instance = null;

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
    protected function __construct()
    {
    }

    /**
     * Disable direct cloning of this object.
     */
    protected function __clone()
    {
    }

    /**
     * Return instance of Bootstrap class as singleton
     *
     * @return Bootstrap
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            self::$instance = new Bootstrap();
        }
        return static::$instance;
    }

    /**
     * initialize basics
     */
    public function initializeBasics()
    {
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
    protected function initializeDefinitions()
    {
        // for php unit testings, we need to check if constant is defined
        // before setting them, because there is a bug in PHPUnit which
        // init our bootstrap multiple times.
        defined('PHILE_VERSION') || define('PHILE_VERSION', '1.7.1');
        defined('PHILE_CLI_MODE') || define('PHILE_CLI_MODE', (php_sapi_name() === 'cli'));
        defined('DS') || define('DS', DIRECTORY_SEPARATOR);
        defined('ROOT_DIR') || define('ROOT_DIR', realpath(__DIR__ . DS . '..' . DS . '..' . DS) . DS);
        defined('CONTENT_DIR') || define('CONTENT_DIR', ROOT_DIR . 'content' . DS);
        defined('CONTENT_EXT') || define('CONTENT_EXT', '.md');
        defined('LIB_DIR') || define('LIB_DIR', ROOT_DIR . 'lib' . DS);
        defined('PLUGINS_DIR') || define('PLUGINS_DIR', ROOT_DIR . 'plugins' . DS);
        defined('THEMES_DIR') || define('THEMES_DIR', ROOT_DIR . 'themes' . DS);
        defined('CACHE_DIR') || define('CACHE_DIR', LIB_DIR . 'cache' . DS);
        defined('STORAGE_DIR') || define('STORAGE_DIR', LIB_DIR . 'datastorage' . DS);
    }

    /**
     * initialize the autoloader
     */
    protected function initializeAutoloader()
    {
        spl_autoload_extensions(".php");
        // load phile core
        spl_autoload_register(
            function ($className) {
                $fileName = LIB_DIR . str_replace("\\", DS, $className) . '.php';
                if (file_exists($fileName)) {
                    include_once $fileName;
                }
            }
        );
        // load phile plugins
        spl_autoload_register('\Phile\Plugin\PluginRepository::autoload');

        include LIB_DIR . 'vendor' . DS . 'autoload.php';
    }

    /**
     * initialize configuration
     */
    protected function initializeConfiguration()
    {
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
    protected function initializeFilesAndFolders()
    {
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
    protected function initializePlugins()
    {
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
    public function getPlugins()
    {
        return $this->plugins;
    }
}
