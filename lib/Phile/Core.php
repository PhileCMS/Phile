<?php
namespace Phile;

/**
 * Phile
 *
 * @author PhileCMS Community, Gilbert Pellegrom(Pico 0.8)
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */

class Core {
	/**
	 * @var array the settings array
	 */
	protected $settings;

	/**
	 * @var array the loaded plugins
	 */
	protected $plugins;

	/**
	 * @var \Phile\Repository\Page
	 */
	protected $pageRepository;

	/**
	 * @var null|\Phile\Model\Page
	 */
	protected $page;

	/**
	 * The constructor carries out all the processing in Phile.
	 * Does URL routing, Markdown processing and Twig processing.
	 */
	public function __construct() {
		$this->pageRepository = new \Phile\Repository\Page();

		// Load the settings
		$this->initConfiguration();

		// Load plugins
		$this->initPlugins();
		/**
		 * @triggerEvent plugins_loaded this event is triggered after the plugins loaded
		 * This is also where we load the parser, since it is a plugin also. We use the Markdown parser as default. See it in the plugins folder and lib/Phile/Parser/Markdown.php
		 */
		Event::triggerEvent('plugins_loaded');

		/**
		 * @triggerEvent config_loaded this event is triggered after the configuration is fully loaded
		 */
		Event::triggerEvent('config_loaded');

		// init current page
		$this->page = $this->initCurrentPage();

		// init template
		echo $this->initTemplate();
	}

	/**
	 * @return null|\Phile\Model\Page
	 */
	protected function initCurrentPage() {
		$uri    = $_SERVER['REQUEST_URI'];
		$uri    = str_replace('/' . \Phile\Utility::getInstallPath() . '/', '', $uri);
		/**
		 * @triggerEvent request_uri this event is triggered after the request uri is detected.
		 * @param uri the uri
		 */
		Event::triggerEvent('request_uri', array('uri' => $uri));

		// use the current url to find the page
		$page = $this->pageRepository->findByPath($_SERVER['REQUEST_URI']);
		if ($page instanceof \Phile\Model\Page) {
			return $page;
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			return $this->pageRepository->findByPath('404');
		}
	}

	/**
	 * init plugins
	 */
	protected function initPlugins() {
		// check to see if there are plugins to be loaded
		if (isset($this->settings['plugins']) && is_array($this->settings['plugins'])) {
			foreach ($this->settings['plugins'] as $pluginKey => $pluginConfig) {
				if (isset($pluginConfig['active']) && $pluginConfig['active'] === true) {
					// load plugin configuration...
					$pluginConfiguration    = null;
					// load the config file for the plugin
					$configFile = \Phile\Utility::resolveFilePath("MOD:{$pluginKey}/config.php");
					if ($configFile !== null) {
						$pluginConfiguration = \Phile\Utility::load($configFile);
						$globalConfiguration = \Phile\Registry::get('Phile_Settings');
						if ($pluginConfiguration !== null && is_array($pluginConfiguration)) {
							$globalConfiguration['plugins'][$pluginKey]['settings'] = $pluginConfiguration;
						} else {
							$globalConfiguration['plugins'][$pluginKey]['settings'] = array();
						}
						\Phile\Registry::set('Phile_Settings', $globalConfiguration);
						$this->settings = $globalConfiguration;
					}
					// uppercase first letter convention
					$pluginClassName    = ucfirst($pluginKey);
					$pluginFile         = \Phile\Utility::resolveFilePath("MOD:{$pluginKey}/plugin.php");
					if ($pluginFile !== null) {
						include_once $pluginFile;
					} else {
						throw new \Phile\Exception("the plugin file 'MOD:{$pluginKey}/plugin.php' not exists");
					}
					if (class_exists($pluginClassName)) {
						$plugin = new $pluginClassName;
						$plugin->injectSettings($pluginConfiguration);
						if ($plugin instanceof \Phile\Plugin\AbstractPlugin) {
							// register plugin
							$this->plugins[$pluginKey] = $plugin;
						} else {
							throw new \Phile\Exception("the plugin '{$pluginKey}' is not an instance of \\Phile\\Plugin\\AbstractPlugin");
						}
					} else {
						throw new \Phile\Exception("the class '{$pluginClassName}' not exists");
					}
				}
			}
		}
	}

	/**
	 * init configuration
	 */
	protected function initConfiguration() {
		$defaults       = Utility::load(ROOT_DIR . '/default_config.php');
		$localSettings  = Utility::load(ROOT_DIR . '/config.php');
		if (is_array($localSettings)) {
			$this->settings = array_replace_recursive($defaults, $localSettings);
		} else {
			$this->settings = $defaults;
		}

		\Phile\Registry::set('Phile_Settings', $this->settings);
		date_default_timezone_set($this->settings['timezone']);
	}

	protected function initTemplate() {
		/**
		 * @triggerEvent before_init_template this event is triggered before the template engine is init
		 */
		Event::triggerEvent('before_init_template');

		$templateEngine   = ServiceLocator::getService('Phile_Template');

		/**
		 * @triggerEvent before_render_template this event is triggered before the template is rendered
		 * @param \Phile\Template\TemplateInterface the template engine
		 */
		Event::triggerEvent('before_render_template', array('templateEngine' => &$templateEngine));

		$templateEngine->setCurrentPage($this->page);
		$output = $templateEngine->render();

		/**
		 * @triggerEvent after_render_template this event is triggered after the template is rendered
		 * @param \Phile\Template\TemplateInterface the template engine
		 * @param string the generated ouput
		 */
		Event::triggerEvent('after_render_template', array('templateEngine' => &$templateEngine, 'output' => &$output));
		return $output;
	}
}
