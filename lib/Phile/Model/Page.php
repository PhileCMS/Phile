<?php

namespace Phile\Model;
use Michelf\MarkdownExtra;

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

	public function __construct($filePath) {
		if (file_exists($filePath)) {
			$this->rawData = file_get_contents($filePath);
			$this->parseRawData();
		}
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
} 