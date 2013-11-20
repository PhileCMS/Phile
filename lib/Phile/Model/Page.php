<?php

namespace Phile\Model;
use Phile\Event;
use Phile\Parser\ParserInterface;
use Phile\Registry;
use Phile\ServiceLocator;
use Phile\Utility;

/**
 * the Model class for a page
 * @author Frank Nägler
 * @package Phile\Model
 */
class Page {
	/**
	 * @var Meta the meta model
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
	 * @var ParserInterface
	 */
	protected $parser;

	/**
	 * @var string returns the path of the page
	 */
	protected $url;

	/**
	 * @param $filePath
	 */
	public function __construct($filePath) {
		$this->filePath = $filePath;

		/**
		 * @triggerEvent before_load_content this event is triggered before the content is loaded
		 * @param string filePath the path to the file
		 * @param \Phile\Model\Page page the page model
		 */
		Event::triggerEvent('before_load_content', array('filePath' => &$this->filePath, 'page' => &$this));
		if (file_exists($this->filePath)) {
			$this->rawData = file_get_contents($this->filePath);
			$this->parseRawData();
		}
		/**
		 * @triggerEvent after_load_content this event is triggered after the content is loaded
		 * @param string filePath the path to the file
		 * @param string rawData the raw data
		 * @param \Phile\Model\Page page the page model
		 */
		Event::triggerEvent('after_load_content', array('filePath' => &$this->filePath, 'rawData' => $this->rawData, 'page' => &$this));
		$this->url  = str_replace(CONTENT_DIR, '', $this->filePath);
		$this->url  = str_replace(CONTENT_EXT, '', $this->url);
		$this->url  = str_replace(DIRECTORY_SEPARATOR, '/', $this->url);
		if (strpos($this->url, '/') === 0) {
			$this->url = substr($this->url, 1);
		}

		$this->parser   = ServiceLocator::getService('Phile_Parser');
	}

	/**
	 * @return mixed
	 */
	public function getContent() {
		/**
		 * @triggerEvent before_parse_content this event is triggered before the content is parsed
		 * @param string content the raw data
		 * @param \Phile\Model\Page page the page model
		 */
		Event::triggerEvent('before_parse_content', array('content' => $this->content, 'page' => &$this));
		$content = $this->parser->parse($this->content);
		/**
		 * @triggerEvent after_parse_content this event is triggered after the content is parsed
		 * @param string content the parsed content
		 * @param \Phile\Model\Page page the page model
		 */
		Event::triggerEvent('after_parse_content', array('content' => &$content, 'page' => &$this));
		return $content;
	}

	/**
	 * @param $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}

	/**
	 * @return Meta
	 */
	public function getMeta() {
		return $this->meta;
	}

	/**
	 * parse the raw content
	 */
	protected function parseRawData() {
		$this->meta     = new Meta($this->rawData);
		// Remove only the first block comment
		$rawData = trim($this->rawData);
		$END    = (substr($rawData, 0, 2) == '/*') ? '*/' : '-->';

		$this->content = substr($this->rawData, strpos($rawData, $END)+strlen($END));
	}

	/**
	 * @return null
	 */
	public function getTitle() {
		return $this->getMeta()->get('title');
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $filePath
	 */
	public function setFilePath($filePath) {
		$this->filePath = $filePath;
	}

	/**
	 * @return string
	 */
	public function getFilePath() {
		return $this->filePath;
	}
}
