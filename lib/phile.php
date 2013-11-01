<?php
use \Michelf\MarkdownExtra;

/**
 * Phile
 *
 * @author PhileCMS Community, Gilbert Pellegrom(Pico 0.8)
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */

class Phile {
	/**
	 * @var array the settings array
	 */
	protected $settings;

	/**
	 * @var array the loaded plugins
	 */
	protected $plugins;

	/**
	 * @var Phile\Repository\Page
	 */
	protected $pageRepository;

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

		// init current page
		$page = $this->initCurrentPage();
		$content = $page->getContent();

		// Get all the pages
		$pages = $this->pageRepository->findAll();

		// Load the theme
		Twig_Autoloader::register();
		$output = 'no template found';
		if (file_exists(THEMES_DIR . $this->settings['theme'])) {
			$loader = new Twig_Loader_Filesystem(THEMES_DIR . $this->settings['theme']);
			$twig = new Twig_Environment($loader, $this->settings['twig_config']);
			$twig->addExtension(new Twig_Extension_Debug());
			$twig_vars = array(
				'config' => $this->settings,
				'base_dir' => rtrim(ROOT_DIR, '/'),
				'base_url' => $this->settings['base_url'],
				'theme_dir' => THEMES_DIR . $this->settings['theme'],
				'theme_url' => $this->settings['base_url'] .'/'. basename(THEMES_DIR) .'/'. $this->settings['theme'],
				'site_title' => $this->settings['site_title'],
				'meta' => $page->getMeta(),
				'content' => $content,
				'pages' => $pages,
#				'prev_page' => $prev_page,
#				'current_page' => $current_page,
#				'next_page' => $next_page,
//				'is_front_page' => $url ? false : true,
			);

			$template = ($page->getMeta()->get('template') !== null) ? $page->getMeta()->get('template') : 'index';
			$this->run_hooks('before_render', array(&$twig_vars, &$twig, &$template));
			$output = $twig->render($template .'.html', $twig_vars);
			$this->run_hooks('after_render', array(&$output));
		}
		echo $output;
	}

	/**
	 * @return null|\Phile\Model\Page
	 */
	protected function initCurrentPage() {
		$page           = $this->pageRepository->findByPath($_SERVER['REQUEST_URI']);
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
		if (isset($this->settings['plugins']) && is_array($this->settings['plugins'])) {
			foreach ($this->settings['plugins'] as $pluginKey => $pluginConfig) {
				if (isset($pluginConfig['active']) && $pluginConfig['active'] === true) {
					// load plugin configuration...
					$pluginConfiguration    = null;
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
		// @TODO: refactor: maybe introduce configuration object
		global $config;
		@include_once(ROOT_DIR .'config.php');

		$defaults = array(
			'site_title' => 'Phile',
			'base_url' => $this->base_url(),
			'theme' => 'default',
			'date_format' => 'jS M Y',
			'twig_config' => array('cache' => false, 'autoescape' => false, 'debug' => false),
			'pages_order_by' => 'alpha',
			'pages_order' => 'asc',
			'excerpt_length' => 50,
			'timezone' => 'Europe/Berlin'
		);

		if(is_array($config)) $config = array_merge($defaults, $config);
		else $config = $defaults;

		$this->settings = $config;
		\Phile\Registry::set('Phile_Settings', $config);

		date_default_timezone_set($config['timezone']);

		$this->run_hooks('config_loaded', array($this->settings));
	}

	/**
	 * Processes any hooks and runs them
	 *
	 * @param string $hook_id the ID of the hook
	 * @param array $args optional arguments
	 * @todo refactor
	 */
	protected function run_hooks($hook_id, $args = array()) {
		if(!empty($this->plugins)){
			foreach($this->plugins as $plugin){
				if(is_callable(array($plugin, $hook_id))){
					call_user_func_array(array($plugin, $hook_id), $args);
				}
			}
		}
	}

	/**
	 * Helper function to work out the base URL
	 *
	 * @return string the base url
	 * @deprecated use \Phile\Utility::getBaseUrl(); instead
	 */
	protected function base_url() {
		return \Phile\Utility::getBaseUrl();
	}

	/**
	 * Tries to guess the server protocol. Used in base_url()
	 *
	 * @return string the current protocol
	 * @deprecated use \Phile\Utility::getProtocol(); instead
	 */
	protected function get_protocol() {
		return \Phile\Utility::getProtocol();
	}
}
