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

		// Load Parser, we use Markdown parser as default
		// to use an other parse, register a plugin and within the plugin, register a new
		// service for "Phile_Parser" which implements the \Phile\Parser\ParserInterface
		\Phile\ServiceLocator::registerService('Phile_Parser', new \Phile\Parser\Markdown());

		// Load plugins
		$this->initPlugins();
		/**
		 * @triggerEvent plugins_loaded this event is triggered after the plugins loaded
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
			$this->settings = array_merge($defaults, $localSettings);
		} else {
			$this->settings = $defaults;
		}

		\Phile\Registry::set('Phile_Settings', $this->settings);
		date_default_timezone_set($this->settings['timezone']);
	}

	protected function initTemplate() {
		// Load the theme
		/**
		 * @triggerEvent before_twig_register this event is triggered before the the twig template engine is registered
		 */
		Event::triggerEvent('before_twig_register');
		\Twig_Autoloader::register();
		// default output
		$output = 'no template found';
		if (file_exists(THEMES_DIR . $this->settings['theme'])) {
			$loader = new \Twig_Loader_Filesystem(THEMES_DIR . $this->settings['theme']);
			$twig = new \Twig_Environment($loader, $this->settings['twig_config']);
			// load the twig debug extension if required
			if ($this->settings['twig_config']['debug']) {
				$twig->addExtension(new \Twig_Extension_Debug());
			}
			$twig_vars = array(
				'config' => $this->settings,
				'base_dir' => rtrim(ROOT_DIR, '/'),
				'base_url' => $this->settings['base_url'],
				'theme_dir' => THEMES_DIR . $this->settings['theme'],
				'theme_url' => $this->settings['base_url'] .'/'. basename(THEMES_DIR) .'/'. $this->settings['theme'],
				'site_title' => $this->settings['site_title'],
				'meta' => $this->page->getMeta(),
				'content' => $this->page->getContent(),
				'pages' => $this->pageRepository->findAll($this->settings),
#				'prev_page' => $prev_page,
#				'current_page' => $current_page,
#				'next_page' => $next_page,
//				'is_front_page' => $url ? false : true,
				);

			$template = ($this->page->getMeta()->get('template') !== null) ? $this->page->getMeta()->get('template') : 'index';
			/**
			 * @triggerEvent before_render this event is triggered before the template is rendered
			 * @param array twig_vars the twig vars
			 * @param object twig the template engine
			 * @param string template the template which will be used
			 */
			Event::triggerEvent('before_render', array('twig_vars' => &$twig_vars, 'twig' => &$twig, 'template' => &$template));
			$output = $twig->render($template .'.html', $twig_vars);
			/**
			 * @triggerEvent after_render this event is triggered after the templates is rendered
			 * @param string output the parsed and ready output
			 */
			Event::triggerEvent('after_render', array('output' => &$output));
		}
		return $output;
	}
}
