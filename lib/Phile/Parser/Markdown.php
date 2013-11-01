<?php
namespace Phile\Parser;

use \Michelf\MarkdownExtra;

class Markdown implements ParserInterface {
	// overload parse with the MarkdownExtra parser
	public function parse($data) {
		return MarkdownExtra::defaultTransform($data);
	}
}
