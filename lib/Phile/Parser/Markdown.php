<?php
namespace Phile\Parser;

use \Michelf\MarkdownExtra;

class Markdown implements ParserInterface {

	public function parse($data) {
		return MarkdownExtra::defaultTransform($data);
	}
} 