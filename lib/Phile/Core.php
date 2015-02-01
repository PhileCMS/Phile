<?php
/**
 * the core of Phile
 */
namespace Phile;
use Phile\Core\Response;
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
	 * @var Bootstrap the bootstrap class
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
	 * @var \Phile\Core\Response
	 */
	protected $response;

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
		$this->response = (new Response)->setCharset($this->settings['charset']);

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
		return $this->response->send();
	}

	/**
	 * initialize the current page
	 */
	protected function initializeCurrentPage() {
		$uri = (strpos($_SERVER['REQUEST_URI'], '?') !== false) ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : $_SERVER['REQUEST_URI'];
		$uri = str_replace('/' . \Phile\Utility::getInstallPath() . '/', '', $uri);
		$uri = (strpos($uri, '/') === 0) ? substr($uri, 1) : $uri;

		// strip '/index' if it exists (as per https://github.com/PhileCMS/Phile/pull/170)
		if ($uri=="index" || preg_match("#/index$#", $uri)>0) {
			// we can't just check if 'index' are the last 5 letters, because then URLs
			// like 'example.com/blog/global-economic-index' would also be stripped...
			$uri = rtrim(Utility::getBaseUrl() . '/' . substr($uri, 0, -5), '/');
			Utility::redirect($uri, 301);
		}

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
			$this->response->setStatusCode(404);
			$this->page = $this->pageRepository->findByPath('404');
		}
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
		$this->response->setBody($output);
	}
}
