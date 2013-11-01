<?php

namespace Phile\Model;
use Michelf\MarkdownExtra;
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

	public function __construct($filePath) {
		/**
		 * @triggerEvent before_load_content this event is triggered before the content is loaded
		 * @param string filePath the path to the file
		 * @param \Phile\Model\Page page the page model
		 */
		Event::triggerEvent('before_load_content', array('filePath' => &$filePath, 'page' => &$this));
		if (file_exists($filePath)) {
			$this->rawData = file_get_contents($filePath);
			$this->parseRawData();
		}
		/**
		 * @triggerEvent after_load_content this event is triggered after the content is loaded
		 * @param string filePath the path to the file
		 * @param string rawData the raw data
		 * @param \Phile\Model\Page page the page model
		 */
		Event::triggerEvent('after_load_content', array('filePath' => &$filePath, 'rawData' => $this->rawData, 'page' => &$this));
		$this->url  = str_replace(CONTENT_DIR, '', $filePath);
		$this->url  = str_replace(CONTENT_EXT, '', $this->url);

		$settings   = Registry::get('Phile_Settings');
		if ($settings['install_path'] !== '') {
			$this->url = $settings['install_path'] . $this->url;
		}

		$this->url  = '/' . $this->url;

		$this->parser   = ServiceLocator::getService('Phile_Parser');
	}

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

	public function setContent($content) {
		$this->content = $content;
	}

	public function getMeta() {
		return $this->meta;
	}

	protected function parseRawData() {
		$this->meta     = new Meta($this->rawData);
		// Remove only the first block comment
		$this->content = str_replace(substr($this->rawData, 0, strpos($this->rawData, "*/") + 2), '', $this->rawData);
	}

	public function getTitle() {
		return $this->getMeta()->get('title');
	}

	public function getUrl() {
		return $this->url;
	}
}
