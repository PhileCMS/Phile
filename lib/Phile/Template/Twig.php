<?php

namespace Phile\Template;

use Phile\Registry;

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
	 * @var \Phile\Model\Page
	 */
	protected $page;

	public function __construct($config = null)	{
		if (!is_null($config)) {
			$this->config = $config;
		}
		$this->settings = Registry::get('Phile_Settings');
	}

	public function setCurrentPage(\Phile\Model\Page $page) {
		$this->page = $page;
	}

	public function render() {
		$pageRepository = new \Phile\Repository\Page();
		$output = 'No template found!';
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
				'current_page' => $this->page,
				'meta' => $this->page->getMeta(),
				'content' => $this->page->getContent(),
				'pages' => $pageRepository->findAll($this->settings),
			);

			$template = ($this->page->getMeta()->get('template') !== null) ? $this->page->getMeta()->get('template') : 'index';
			$output = $twig->render($template .'.html', $twig_vars);
		}
		return $output;
	}
}
