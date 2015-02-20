<?php
/**
 * the core of Phile
 */
namespace Phile;
use Phile\Core\Request;
use Phile\Core\Response;
use Phile\Core\Router;
use Phile\Model\Page;
use Phile\Utility;
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
	 * @var null|Page the page model
	 */
	protected $page;

	/**
	 * @var string the output (rendered page)
	 */
	protected $output;

	/**
	 * @var \Phile\Core\Response the response the core send
	 */
	protected $response;

	/**
	 * @var string relative URL of current request
	 */
	protected $pageId;

	/**
	 * The constructor carries out all the processing in Phile.
	 * Does URL routing, Markdown processing and Twig processing.
	 *
	 * @param Response $response
	 * @throws \Exception
	 */
	public function __construct(Response $response) {
		$this->settings = \Phile\Registry::get('Phile_Settings');

		$this->response = $response;
		$this->response->setCharset($this->settings['charset']);

		$this->pageRepository = new \Phile\Repository\Page();

		// Setup Check
		$this->checkSetup();

		// init error handler
		$this->initializeErrorHandling();

		// init current page
		$this->resolveUrl();
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
		$this->response->send();
	}

	/**
	 * detect URL, perform necessary redirects and resolve page-Id
	 */
	protected function resolveUrl() {
		$url = Request::getUrl();
		$tidy = Router::tidyUrl($url);
		if ($url !== $tidy) {
			$this->response->setStatusCode(301)->redirect(Router::url($tidy));
			return;
		}
		Event::triggerEvent('request_uri', ['uri' => $url]);
		$this->pageId = $url;
	}

	/**
	 * initialize the current page
	 */
	protected function initializeCurrentPage() {
		$page = $this->pageRepository->findByPath($this->pageId);
		if ($page instanceof Page) {
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
			if (Request::getUrl() !== 'setup') {
				$this->response->redirect(Router::url('setup'));
				return;
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
