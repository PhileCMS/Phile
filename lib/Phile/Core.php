<?php
/**
 * the core of Phile
 */
namespace Phile;
use Phile\Exception\PluginException;

/**
 * Phile
 *
 * @author  PhileCMS Community, Gilbert Pellegrom(Pico 0.8)
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Core {
	/**
	 * @var Bootstrap
	 */
	protected $bootstrap;

	/**
	 * @var array the settings array
	 */
	protected $settings;

	/**
	 * @var array the loaded plugins
	 */
	protected $plugins;

	/**
	 * @var \Phile\Repository\Page the page repository
	 */
	protected $pageRepository;

	/**
	 * @var null|\Phile\Model\Page the page model
	 */
	protected $page;

	/**
	 * @var string the output (rendered page)
	 */
	protected $output;

	/**
	 * The constructor carries out all the processing in Phile.
	 * Does URL routing, Markdown processing and Twig processing.
	 *
	 * @param Bootstrap $bootstrap
	 */
	public function __construct(Bootstrap $bootstrap) {
		$this->bootstrap = $bootstrap;

		$this->settings = \Phile\Registry::get('Phile_Settings');

		$this->pageRepository = new \Phile\Repository\Page();

		// Setup Check
		$this->checkSetup();

		// init error handler
		$this->initializeErrorHandling();

		// init current page
		$this->initializeCurrentPage();

		// init template
		$this->initializeTemplate();
	}

	/**
	 * return the page
	 *
	 * @return string
	 */
	public function render() {
		return $this->output;
	}

	/**
	 * initialize the current page
	 */
	protected function initializeCurrentPage() {
		$uri = (strpos($_SERVER['REQUEST_URI'], '?') !== false) ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : $_SERVER['REQUEST_URI'];
		$uri = str_replace('/' . \Phile\Utility::getInstallPath() . '/', '', $uri);
		$uri = (strpos($uri, '/') === 0) ? substr($uri, 1) : $uri;
		/**
		 * @triggerEvent request_uri this event is triggered after the request uri is detected.
		 *
		 * @param uri the uri
		 */
		Event::triggerEvent('request_uri', array('uri' => $uri));

		// use the current url to find the page
		$page = $this->pageRepository->findByPath($uri);
		if ($page instanceof \Phile\Model\Page) {
			$this->page = $page;
		} else {
			header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
			$this->page = $this->pageRepository->findByPath('404');
		}
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
	 * initialize error handling
	 */
	protected function initializeErrorHandling() {
		if (ServiceLocator::hasService('Phile_ErrorHandler')) {
			$errorHandler = ServiceLocator::getService('Phile_ErrorHandler');
			set_error_handler(array($errorHandler, 'handleError'));
		}
	}

	/**
	 * check the setup
	 */
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
			if (is_file(CONTENT_DIR . 'setup.md')) {
				unlink(CONTENT_DIR . 'setup.md');
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

		$templateEngine = ServiceLocator::getService('Phile_Template');

		/**
		 * @triggerEvent before_render_template this event is triggered before the template is rendered
		 *
		 * @param \Phile\ServiceLocator\TemplateInterface the template engine
		 */
		Event::triggerEvent('before_render_template', array('templateEngine' => &$templateEngine));

		$templateEngine->setCurrentPage($this->page);
		$output = $templateEngine->render();

		/**
		 * @triggerEvent after_render_template this event is triggered after the template is rendered
		 *
		 * @param \Phile\ServiceLocator\TemplateInterface the    template engine
		 * @param                                         string the generated ouput
		 */
		Event::triggerEvent('after_render_template', array('templateEngine' => &$templateEngine, 'output' => &$output));
		$this->output = $output;
	}
}
