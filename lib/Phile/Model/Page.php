<?php
/**
 * The page model
 */
namespace Phile\Model;

use Phile\Core\Router;
use Phile\Core\Event;
use Phile\Core\ServiceLocator;

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
	 * @var string the pageId of the page
	 */
	protected $pageId;

	/**
	 * @var \Phile\Model\Page the previous page if one exist
	 */
	protected $previousPage;

	/**
	 * @var \Phile\Model\Page the next page if one exist
	 */
	protected $nextPage;

	/**
	 * @var string The content folder, as passed to the class constructor when initiating the object.
	 */
	protected $contentFolder = CONTENT_DIR;

	/**
	 * the constructor
	 *
	 * @param        $filePath
	 * @param string $folder
	 */
	public function __construct($filePath, $folder = CONTENT_DIR) {
		$this->contentFolder = $folder;
		$this->setFilePath($filePath);

		/**
		 * @triggerEvent before_load_content this event is triggered before the content is loaded
		 *
		 * @param            string filePath the path to the file
		 * @param \Phile\Model\Page page     the page model
		 */
		Event::triggerEvent('before_load_content', array('filePath' => &$this->filePath, 'page' => &$this));
		if (file_exists($this->filePath)) {
			$this->rawData = file_get_contents($this->filePath);
			$this->parseRawData();
		}
		/**
		 * @triggerEvent after_load_content this event is triggered after the content is loaded
		 *
		 * @param            string filePath the path to the file
		 * @param            string rawData  the raw data
		 * @param \Phile\Model\Page page     the page model
		 */
		Event::triggerEvent('after_load_content', array('filePath' => &$this->filePath, 'rawData' => $this->rawData, 'page' => &$this));

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
		 * @param            string content the raw data
		 * @param \Phile\Model\Page page    the page model
		 */
		Event::triggerEvent('before_parse_content', array('content' => $this->content, 'page' => &$this));
		$content = $this->parser->parse($this->content);
		/**
		 * @triggerEvent after_parse_content this event is triggered after the content is parsed
		 *
		 * @param            string content the parsed content
		 * @param \Phile\Model\Page page    the page model
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
	 * get raw (un-parsed) page content
	 *
	 * @return string
	 */
	public function getRawContent() {
		return $this->content;
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
	 * get Phile $pageId
	 *
	 * @param string $filePath
	 * @return string
	 */
	protected function buildPageId($filePath) {
		$pageId = str_replace($this->contentFolder, '', $filePath);
		$pageId = str_replace(CONTENT_EXT, '', $pageId);
		$pageId = str_replace(DIRECTORY_SEPARATOR, '/', $pageId);
		$pageId = ltrim($pageId, '/');
		$pageId = preg_replace('/(?<=^|\/)index$/', '', $pageId);
		return $pageId;
	}

	/**
	 * get the url of page
	 *
	 * @return string
	 */
	public function getUrl() {
		return (new Router)->urlForPage($this->pageId, false);
	}

	/**
	 * set the filepath of the page
	 *
	 * @param string $filePath
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
		$this->pageId = $this->buildPageId($this->filePath);
	}

	/**
	 * get the filepath of the page
	 *
	 * @return string
	 */
	public function getFilePath() {
		return $this->filePath;
	}

	/**
	 * get the folder name
	 *
	 * @return string
	 */
	public function getFolder() {
		return basename(dirname($this->getFilePath()));
	}

	public function getPageId() {
		return $this->pageId;
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
