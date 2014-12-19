<?php
/**
 * Template engine class
 */
namespace Phile\Plugin\Phile\TemplateTwig\Template;

use Phile\ServiceLocator\TemplateInterface;

/**
 * Class Twig
 *
 * @author  Frank Nägler
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
	 * @var \Phile\Model\Page the page model
	 */
	protected $page;

	/**
	 * the constructor
	 *
	 * @param mixed $config the configuration
	 */
	public function __construct($config = null) {
		if (!is_null($config)) {
			$this->config = $config;
		}
		$this->settings = \Phile\Core\Registry::get('Phile_Settings');
	}

	/**
	 * method to set the current page
	 *
	 * @param \Phile\Model\Page $page the page model
	 *
	 * @return mixed|void
	 */
	public function setCurrentPage(\Phile\Model\Page $page) {
		$this->page = $page;
	}

	/**
	 * method to render the page/template
	 *
	 * @return mixed|string
	 */
	public function render() {
		$pageRepository = new \Phile\Repository\Page($this->settings);
		$output         = 'No template found!';
		if (file_exists(THEMES_DIR . $this->settings['theme'])) {
			$loader = new \Twig_Loader_Filesystem(THEMES_DIR . $this->settings['theme']);
			$twig   = new \Twig_Environment($loader, $this->config);
			// load the twig debug extension if required
			if ($this->config['debug']) {
				$twig->addExtension(new \Twig_Extension_Debug());
			}
			$twig_vars = array(
				'config'       => $this->settings,
				'base_dir'     => rtrim(ROOT_DIR, '/'),
				'base_url'     => $this->settings['base_url'],
				'theme_dir'    => THEMES_DIR . $this->settings['theme'],
				'theme_url'    => $this->settings['base_url'] . '/' . basename(THEMES_DIR) . '/' . $this->settings['theme'],
				'content_dir'  => CONTENT_DIR,
				'content_url'  => $this->settings['base_url'] . '/' . basename(CONTENT_DIR),
				'site_title'   => $this->settings['site_title'],
				'current_page' => $this->page,
				'meta'         => $this->page->getMeta(),
				'content'      => $this->page->getContent(),
				'pages'        => $pageRepository->findAll(),
			);

			if (\Phile\Core\Registry::isRegistered('templateVars')) {
				if (is_array(\Phile\Core\Registry::get('templateVars'))) {
					foreach (\Phile\Core\Registry::get('templateVars') as $key => $value) {
						$twig_vars[$key] = $value;
					}
				}
			}
			\Phile\Core\Event::triggerEvent('template_engine_registered', array('engine' => &$twig, 'data' => &$twig_vars));

			$file = $twig_vars['theme_dir'] . '/' . $this->page->getMeta()->get('template') . '.html';
			if ($this->page->getMeta()->get('template') !== null && file_exists($file)) {
				$template = $this->page->getMeta()->get('template');
			} else {
				$template = 'index';
			}

			$output = $twig->render($template . '.html', $twig_vars);
		}

		return $output;
	}
}
