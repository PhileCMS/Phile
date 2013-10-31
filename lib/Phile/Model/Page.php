<?php

namespace Phile\Model;
use Michelf\MarkdownExtra;
use Phile\Registry;
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
	 * @var string returns the path of the page
	 */
	protected $url;

	public function __construct($filePath) {
		if (file_exists($filePath)) {
			$this->rawData = file_get_contents($filePath);
			$this->parseRawData();
		}
		$this->url  = str_replace(CONTENT_DIR, '', $filePath);
		$this->url  = str_replace(CONTENT_EXT, '', $this->url);

		$settings   = Registry::get('Phile_Settings');
		if ($settings['install_path'] !== '') {
			$this->url = $settings['install_path'] . $this->url;
		}

		$this->url  = '/' . $this->url;
	}

	public function getContent() {
		// @TODO: implement parser interface to switch parser by configuration...
		return MarkdownExtra::defaultTransform($this->content);
	}

	public function getMeta() {
		return $this->meta;
	}

	protected function parseRawData() {
		$this->meta     = new Meta($this->rawData);
		$this->content  = preg_replace('#/\*.+?\*/#s', '', $this->rawData); // Remove comments and meta
	}

	public function getTitle() {
		return $this->getMeta()->get('title');
	}

	public function getUrl() {
		return $this->url;
	}
} 