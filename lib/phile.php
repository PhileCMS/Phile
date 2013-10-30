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

		// Load plugins
		$this->initPlugins();

		// Load the settings
		$this->initConfiguration();

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
		// @TODO: refactor
		$this->plugins = array();
		$plugins = $this->get_files(PLUGINS_DIR, '.php');
		if(!empty($plugins)){
			foreach($plugins as $plugin){
				include_once($plugin);
				$plugin_name = preg_replace("/\\.[^.\\s]{3}$/", '', basename($plugin));
				if(class_exists($plugin_name)){
					$obj = new $plugin_name;
					$this->plugins[] = $obj;
				}
			}
		}
		$this->run_hooks('plugins_loaded');
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
	 * Parses the content using Markdown
	 *
	 * @param string $content the raw txt content
	 * @return string $content the Markdown formatted content
	 */
	protected function parse_content($content)
	{
		$content = preg_replace('#/\*.+?\*/#s', '', $content); // Remove comments and meta
		$content = str_replace('%base_url%', $this->base_url(), $content);
		$content = MarkdownExtra::defaultTransform($content);

		return $content;
	}

	/**
	 * Parses the file meta from the txt file header
	 *
	 * @param string $content the raw txt content
	 * @return array $headers an array of meta values
	 * @deprecated
	 */
	protected function read_file_meta($content)
	{
		global $config;

		$headers = array(
			'title'       	=> 'Title',
			'description' 	=> 'Description',
			'author' 		=> 'Author',
			'date' 			=> 'Date',
			'robots'     	=> 'Robots',
			'template'      => 'Template'
			);

		// Add support for custom headers by hooking into the headers array
		$this->run_hooks('before_read_file_meta', array(&$headers));

		foreach ($headers as $field => $regex){
			if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $content, $match) && $match[1]){
				$headers[ $field ] = trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $match[1]));
			} else {
				$headers[ $field ] = '';
			}
		}

		if(isset($headers['date'])) $headers['date_formatted'] = date($config['date_format'], strtotime($headers['date']));

		return $headers;
	}

	/**
	 * Get a list of pages
	 *
	 * @param string $base_url the base URL of the site
	 * @param string $order_by order by "alpha" or "date"
	 * @param string $order order "asc" or "desc"
	 * @return array $sorted_pages an array of pages
	 * @deprecated use \Phile\Repository\Page::findAll() instead
	 */
	protected function get_pages($base_url, $order_by = 'alpha', $order = 'asc', $excerpt_length = 50) {
		return $this->pageRepository->findAll(array('order_by' => $order_by, 'order' => $order));
	}

	/**
	 * Processes any hooks and runs them
	 *
	 * @param string $hook_id the ID of the hook
	 * @param array $args optional arguments
	 * @todo refactor
	 */
	protected function run_hooks($hook_id, $args = array())
	{
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
	 */
	protected function base_url()
	{
		global $config;
		if(isset($config['base_url']) && $config['base_url']) return $config['base_url'];

		$url = '';
		$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
		$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
		if($request_url != $script_url) $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

		$protocol = $this->get_protocol();
		return rtrim(str_replace($url, '', $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']), '/');
	}

	/**
	 * Tries to guess the server protocol. Used in base_url()
	 *
	 * @return string the current protocol
	 */
	protected function get_protocol()
	{
		$protocol = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
			$protocol = 'https';
		}
		return $protocol;
	}

	/**
	 * Helper function to recusively get all files in a directory
	 *
	 * @param string $directory start directory
	 * @param string $ext optional limit to file extensions
	 * @return array the matched files
	 */
	protected function get_files($directory, $ext = '')
	{
		$array_items = array();
		if($handle = opendir($directory)){
			while(false !== ($file = readdir($handle))){
				if(preg_match("/^(^\.)/", $file) === 0){
					if(is_dir($directory. "/" . $file)){
						$array_items = array_merge($array_items, $this->get_files($directory. "/" . $file, $ext));
					} else {
						$file = $directory . "/" . $file;
						if(!$ext || strstr($file, $ext)) $array_items[] = preg_replace("/\/\//si", "/", $file);
					}
				}
			}
			closedir($handle);
		}
		return $array_items;
	}

	/**
	 * Helper function to limit the words in a string
	 *
	 * @param string $string the given string
	 * @param int $word_limit the number of words to limit to
	 * @return string the limited string
	 */
	protected function limit_words($string, $word_limit)
	{
		$words = explode(' ',$string);
		$excerpt = trim(implode(' ', array_splice($words, 0, $word_limit)));
		if(count($words) > $word_limit) $excerpt .= '&hellip;';
		return $excerpt;
	}

}
