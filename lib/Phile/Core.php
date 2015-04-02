<?php
/**
 * the core of Phile
 */
namespace Phile;

use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Request;
use Phile\Core\Response;
use Phile\Core\Router;
use Phile\Core\ServiceLocator;
use Phile\Core\Utility;
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
		$this->settings = Registry::get('Phile_Settings');

		$this->pageRepository = new Repository();
		$this->router = $router;
		$this->response = $response;
		$this->response->setCharset($this->settings['charset']);

		$this->initializeErrorHandling();
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

	/**
	 * initialize the current page
	 */
	protected function initializeCurrentPage() {
		$pageId = $this->router->getCurrentUrl();
		$page = $this->pageRepository->findByPath($pageId);

		if (!($page instanceof Page)) {
			$this->response->setStatusCode(404);
			$page = $this->pageRepository->findByPath('404');
		} elseif ($pageId !== $page->getPageId()) {
			$redirect = $this->router->urlForPage($page->getPageId());
			$this->response->redirect($redirect, 301);
		}

		Event::triggerEvent('request_uri', ['uri' => $pageId]);
		$this->page = $page;
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

		if (!Registry::isRegistered('templateVars')) {
			Registry::set('templateVars', []);
		}

		if (!isset($this->settings['encryptionKey']) || strlen($this->settings['encryptionKey']) == 0) {
			if ($this->router->getCurrentUrl() !== 'setup') {
				$this->response->redirect($this->router->url('setup'));
				return;
			}
		} else {
			if (is_file(CONTENT_DIR . 'setup.md')) {
				unlink(CONTENT_DIR . 'setup.md');
			}
		}
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
