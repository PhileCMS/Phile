<?php
/**
 * The page model
 */
namespace Phile\Model;

use Phile\Event;
use Phile\ServiceLocator;

/**
 * the Model class for a page
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Model
 */
class Page {
	/**
	 * @var \Phile\Model\Meta the meta model
	 */
	protected $meta;

	/**
	 * @var string the content
	 */
	protected $content;

	/**
	 * @var string the path to the original file
	 */
	protected $filePath;

	/**
	 * @var string the raw file
	 */
	protected $rawData;

	/**
	 * @var \Phile\ServiceLocator\ParserInterface the parser
	 */
	protected $parser;

	/**
	 * @var string returns the path of the page
	 */
	protected $url;

	/**
	 * @var \Phile\Model\Page the previous page if one exist
	 */
	protected $previousPage;

	/**
	 * @var \Phile\Model\Page the next page if one exist
	 */
	protected $nextPage;

	/**
	 * the constructor
	 *
	 * @param        $filePath
	 * @param string $folder
	 */
	public function __construct($filePath, $folder = CONTENT_DIR) {
		$this->filePath = $filePath;

		/**
		 * @triggerEvent before_load_content this event is triggered before the content is loaded
		 *
		 * @param                   string filePath the path to the file
		 * @param \Phile\Model\Page page   the page model
		 */
		Event::triggerEvent('before_load_content', array('filePath' => &$this->filePath, 'page' => &$this));
		if (file_exists($this->filePath)) {
			$this->rawData = file_get_contents($this->filePath);
			$this->parseRawData();
		}
		/**
		 * @triggerEvent after_load_content this event is triggered after the content is loaded
		 *
		 * @param                   string filePath the path to the file
		 * @param                   string rawData the raw data
		 * @param \Phile\Model\Page page   the page model
		 */
		Event::triggerEvent('after_load_content', array('filePath' => &$this->filePath, 'rawData' => $this->rawData, 'page' => &$this));
		$this->url = str_replace($folder, '', $this->filePath);
		$this->url = str_replace(CONTENT_EXT, '', $this->url);
		$this->url = str_replace(DIRECTORY_SEPARATOR, '/', $this->url);
		$this->url = preg_replace('#(.*)/?index$#', '$1', $this->url);
		$this->url = ltrim($this->url, '/');

		$this->parser = ServiceLocator::getService('Phile_Parser');
	}

	/**
	 * method to get content of page, this method returned the parsed content
	 *
	 * @return mixed
	 */
	public function getContent() {
		/**
		 * @triggerEvent before_parse_content this event is triggered before the content is parsed
		 *
		 * @param                   string content the raw data
		 * @param \Phile\Model\Page page   the page model
		 */
		Event::triggerEvent('before_parse_content', array('content' => $this->content, 'page' => &$this));
		$content = $this->parser->parse($this->content);
		/**
		 * @triggerEvent after_parse_content this event is triggered after the content is parsed
		 *
		 * @param                   string content the parsed content
		 * @param \Phile\Model\Page page   the page model
		 */
		Event::triggerEvent('after_parse_content', array('content' => &$content, 'page' => &$this));

		return $content;
	}

	/**
	 * set content of page
	 *
	 * @param $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * get the meta model
	 *
	 * @return Meta
	 */
	public function getMeta() {
		return $this->meta;
	}

	/**
	 * parse the raw content
	 */
	protected function parseRawData() {
		$this->meta = new Meta($this->rawData);
		// Remove only the optional, leading meta-block comment
		$rawData = trim($this->rawData);
		if (strncmp('<!--', $rawData, 4) === 0) {
			// leading meta-block comment uses the <!-- --> style
			$this->content = substr($rawData, max(4, strpos($rawData, '-->') + 3));
		} elseif (strncmp('/*', $rawData, 2) === 0) {
			// leading meta-block comment uses the /* */ style
			$this->content = substr($rawData, strpos($rawData, '*/') + 2);
		} else {
			// no leading meta-block comment
			$this->content = $rawData;
		}
	}

	/**
	 * get the title of page from meta information
	 *
	 * @return string|null
	 */
	public function getTitle() {
		return $this->getMeta()->get('title');
	}

	/**
	 * get the url of page
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * set the filepath of the page
	 *
	 * @param string $filePath
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
	}

	/**
	 * get the filepath of the page
	 *
	 * @return string
	 */
	public function getFilePath() {
		return $this->filePath;
	}

	public function getFolder() {
		return basename(dirname($this->getFilePath()));
	}

	/**
	 * get the previous page if one exist
	 *
	 * @return null|\Phile\Model\Page
	 */
	public function getPreviousPage() {
		$pageRepository = new \Phile\Repository\Page();
		return $pageRepository->getPageOffset($this, -1);
	}

	/**
	 * get the next page if one exist
	 *
	 * @return null|\Phile\Model\Page
	 */
	public function getNextPage() {
		$pageRepository = new \Phile\Repository\Page();
		return $pageRepository->getPageOffset($this, 1);
	}
}
