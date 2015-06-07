<?php
/**
 * the core of Phile
 */
namespace Phile;

use Phile\Core\Response;
use Phile\Core\Router;
use Phile\Model\Page;
use Phile\Repository\Page as Repository;

/**
 * Phile Core class
 *
 * @author  PhileCMS
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
	 * @var null|\Phile\Model\Page the page model
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
	 * @var Router
	 */
	protected $router;

	/**
	 * The constructor carries out all the processing in Phile.
	 * Does URL routing, Markdown processing and Twig processing.
	 *
	 * @param Router $router
	 * @param Response $response
	 * @throws \Exception
	 */
	public function __construct(Router $router, Response $response) {
		$this->initializeErrorHandling();
		$this->initialize($router, $response);
		$this->checkSetup();
		$this->initializeCurrentPage();
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

	protected function initialize(Router $router, Response $response) {
		$this->settings = Registry::get('Phile_Settings');
		$this->pageRepository = new Repository();
		$this->router = $router;
		$this->response = $response;
		$this->response->setCharset($this->settings['charset']);

		Event::triggerEvent('after_init_core', ['response' => $this->response]);
	}

	/**
	 * initialize the current page
	 */
	protected function initializeCurrentPage() {
		$pageId = $this->router->getCurrentUrl();

		Event::triggerEvent('request_uri', ['uri' => $pageId]);

		$page = $this->pageRepository->findByPath($pageId);
		$found = $page instanceof Page;

		if ($found && $pageId !== $page->getPageId()) {
			$url = $this->router->urlForPage($page->getPageId());
			$this->response->redirect($url, 301);
		}

		if (!$found) {
			$this->response->setStatusCode(404);
			$page = $this->pageRepository->findByPath('404');
			Event::triggerEvent('after_404');
		}

		Event::triggerEvent('after_resolve_page', ['pageId' => $pageId, 'page' => &$page]);

		$this->page = $page;
	}

	/**
	 * initialize error handling
	 */
	protected function initializeErrorHandling() {
		if (ServiceLocator::hasService('Phile_ErrorHandler')) {
			$errorHandler = ServiceLocator::getService('Phile_ErrorHandler');
			set_error_handler([$errorHandler, 'handleError']);
			register_shutdown_function([$errorHandler, 'handleShutdown']);
			ini_set('display_errors', $this->settings['display_errors']);
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

		if (!Registry::isRegistered('templateVars')) {
			Registry::set('templateVars', []);
		}

		Event::triggerEvent('setup_check');

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
