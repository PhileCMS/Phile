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
	 * @var string
	 */
	protected $output;

	/**
	 * The constructor carries out all the processing in Phile.
	 * Does URL routing, Markdown processing and Twig processing.
	 */
	public function __construct() {
		$this->pageRepository = new \Phile\Repository\Page();

		// Load the settings
		$this->initConfiguration();

		// Setup Check
		$this->checkSetup();

		// Load plugins
		$this->initPlugins();

		// init current page
		$this->initCurrentPage();

		// init template
		$this->initTemplate();
	}

	/**
	 * return the page
	 * @return string
	 */
	public function render() {
		return $this->output;
	}

	/**
	 * @return null
	 */
	protected function initCurrentPage() {
		$uri    = (strpos($_SERVER['REQUEST_URI'], '?') !== false) ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : $_SERVER['REQUEST_URI'];
		$uri    = str_replace('/' . \Phile\Utility::getInstallPath() . '/', '', $uri);
		/**
		 * @triggerEvent request_uri this event is triggered after the request uri is detected.
		 * @param uri the uri
		 */
		Event::triggerEvent('request_uri', array('uri' => $uri));

		// use the current url to find the page
		$page = $this->pageRepository->findByPath($uri);
		if ($page instanceof \Phile\Model\Page) {
			$this->page = $page;
		} else {
			header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			$this->page = $this->pageRepository->findByPath('404');
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
		/**
		 * @triggerEvent plugins_loaded this event is triggered after the plugins loaded
		 * This is also where we load the parser, since it is a plugin also. We use the Markdown parser as default. See it in the plugins folder and lib/Phile/Parser/Markdown.php
		 */
		Event::triggerEvent('plugins_loaded');

		/**
		 * @triggerEvent config_loaded this event is triggered after the configuration is fully loaded
		 */
		Event::triggerEvent('config_loaded');
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

	protected function checkSetup() {
		/**
		 * @triggerEvent before_setup_check this event is triggered before the setup check
		 */
		Event::triggerEvent('before_setup_check');

		if (!isset($this->settings['encryptionKey']) || strlen($this->settings['encryptionKey']) == 0) {
			if (strpos($_SERVER['REQUEST_URI'], '/setup') === false) {
				Utility::redirect($this->settings['base_url'] . '/setup');
			}
		} else {
			if (is_file(CONTENT_DIR.'setup.md')) {
				unlink(CONTENT_DIR.'setup.md');
			}
		}
		if (Registry::isRegistered('templateVars')) {
			$templateVars = Registry::get('templateVars');
		} else {
			$templateVars = array();
		}
		$templateVars['setup_enrcyptionKey'] = Utility::generateSecureToken(64);
		Registry::set('templateVars', $templateVars);

		/**
		 * @triggerEvent after_setup_check this event is triggered after the setup check
		 */
		Event::triggerEvent('after_setup_check');
	}

	/**
	 * initialize template engine
	 */
	protected function initializeTemplate() {
		/**
		 * @triggerEvent before_init_template this event is triggered before the template engine is init
		 */
		Event::triggerEvent('before_init_template');

		$templateEngine   = ServiceLocator::getService('Phile_Template');

		/**
		 * @triggerEvent before_render_template this event is triggered before the template is rendered
		 * @param \Phile\ServiceLocator\TemplateInterface the template engine
		 */
		Event::triggerEvent('before_render_template', array('templateEngine' => &$templateEngine));

		$templateEngine->setCurrentPage($this->page);
		$output = $templateEngine->render();

		/**
		 * @triggerEvent after_render_template this event is triggered after the template is rendered
		 * @param \Phile\ServiceLocator\TemplateInterface the template engine
		 * @param string the generated ouput
		 */
		Event::triggerEvent('after_render_template', array('templateEngine' => &$templateEngine, 'output' => &$output));
		$this->output = $output;
	}
}
