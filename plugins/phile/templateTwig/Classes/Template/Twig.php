<?php
/**
 * Template engine class
 */
namespace Phile\Plugin\Phile\TemplateTwig\Template;

use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Model\Page;
use Phile\Repository\Page as Repository;
use Phile\ServiceLocator\TemplateInterface;

/**
 * Class Twig
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\TemplateTwig\Template
 */
class Twig implements TemplateInterface {
	/**
	 * @var array the complete phile config
	 */
	protected $settings;

	/**
	 * @var array the config for twig
	 */
	protected $config;

	/**
	 * @var Page the page model
	 */
	protected $page;

	/**
	 * the constructor
	 *
	 * @param array $config the configuration
	 */
	public function __construct($config = []) {
		$this->config = $config;
		$this->settings = Registry::get('Phile_Settings');
	}

	/**
	 * method to set the current page
	 *
	 * @param Page $page the page model
	 *
	 * @return mixed|void
	 */
	public function setCurrentPage(Page $page) {
		$this->page = $page;
	}

	/**
	 * method to render the page/template
	 *
	 * @return mixed|string
	 */
	public function render() {
		$engine = $this->getEngine();
		$vars = $this->getTemplateVars();

		Event::triggerEvent(
			'template_engine_registered',
			['engine' => &$engine, 'data' => &$vars]
		);

		return $this->_render($engine, $vars);
	}

	/**
	 * wrapper to call the render engine
	 *
	 * @param $engine
	 * @param $vars
	 * @return mixed
	 */
	protected function _render($engine, $vars) {
		try {
			$template = $this->getTemplateFileName();
		} catch (\RuntimeException $e) {
			return $e->getMessage();
		}
		return $engine->render($template, $vars);
	}

	/**
	 * get template engine
	 *
	 * @return \Twig_Environment
	 */
	protected function getEngine() {
		$loader = new \Twig_Loader_Filesystem($this->getTemplatePath());
		$twig = new \Twig_Environment($loader, $this->config);

		// load the twig debug extension if required
		if (!empty($this->config['debug'])) {
			$twig->addExtension(new \Twig_Extension_Debug());
		}
		return $twig;
	}

	/**
	 * get template file name
	 *
	 * @return string
	 * @throws \RuntimeException
	 */
	protected function getTemplateFileName() {
		$template = $this->page->getMeta()->get('template');
		if (empty($template)) {
			$template = 'index';
		}
		if (!empty($this->config['template-extension'])) {
			$template .= '.' . $this->config['template-extension'];
		}
		$templatePath = $this->getTemplatePath($template);
		if (!file_exists($templatePath)) {
			throw new \RuntimeException(
				"Template file '{$templatePath}' not found.",
				1427990135
			);
		}
		return $template;
	}

	/**
	 * get file path to (sub-path) in theme-path
	 *
	 * @param string $sub
	 * @return string
	 */
	protected function getTemplatePath($sub = '') {
		$themePath = THEMES_DIR . $this->settings['theme'];
		if (!empty($sub)) {
			$themePath .= '/' . ltrim($sub, DIRECTORY_SEPARATOR);
		}
		return $themePath;
	}

	/**
	 * get template vars
	 *
	 * @return array|mixed
	 * @throws \Exception
	 */
	protected function getTemplateVars() {
		$repository = new Repository($this->settings);
		$defaults = [
			'content' => $this->page->getContent(),
			'meta' => $this->page->getMeta(),
			'current_page' => $this->page,
			'base_dir' => rtrim(ROOT_DIR, '/'),
			'base_url' => $this->settings['base_url'],
			'config' => $this->settings,
			'content_dir' => CONTENT_DIR,
			'content_url' => $this->settings['base_url'] . '/' . basename(CONTENT_DIR),
			'pages' => $repository->findAll(),
			'site_title' => $this->settings['site_title'],
			'theme_dir' => THEMES_DIR . $this->settings['theme'],
			'theme_url' => $this->settings['base_url'] . '/' . basename(THEMES_DIR) . '/' . $this->settings['theme'],
		];

		/** @var array $templateVars */
		$templateVars = Registry::get('templateVars');
		$templateVars += $defaults;

		return $templateVars;
	}

}
