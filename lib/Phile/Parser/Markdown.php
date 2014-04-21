<?php
namespace Phile\Parser;

use \Michelf\MarkdownExtra;
use Phile\ServiceLocator\ParserInterface;

class Markdown implements ParserInterface {
	private $config;

	/**
	 * @param null $config
	 */
	public function __construct($config = null) {
		if (!is_null($config)) {
			$this->config = $config;
		}
	}

	/**
	 * overload parse with the MarkdownExtra parser
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public function parse($data) {
		$parser = new MarkdownExtra;
		foreach ($this->config as $key => $value) {
			$parser->{$key} = $value;
		}

		return $parser->transform($data);
	}
}
