<?php
/**
 * page generator for phing build system
 *
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Task;
 */

require_once __DIR__ . '/../Bootstrap.php';

\Phile\Bootstrap::getInstance()->initializeBasics();

/**
 * phing Task for creating dummy pages
 */
class PageGeneratorTask extends Task {

	/**
	 * @var array
	 */
	protected $settings = [
		'root' => 'sub/',
		'max' => 10,
	];

	/**
	 * set content sub-folder from phing
	 *
	 * @param $string
	 */
	public function setRoot($string) {
		$this->settings['root'] = $string;
	}

	/**
	 * set max items to create from phing
	 *
	 * @param $string
	 */
	public function setMax($string) {
		$this->settings['max'] = (int)$string;
	}

	/**
	 * main method called by phing
	 */
	public function main() {
		$current = 0;
		$max = $this->settings['max'] + 1;
		while (++$current < $max) {
			$content = $this->getContent();
			$title = 'dummy-' . $current . '.md';
			$path = rtrim($this->settings['root'], '/') . '/' . $title;
			$this->createFile($path, $content);
		}
	}

	/**
	 * create a content page
	 *
	 * @param $path sub-path in content folder
	 * @param $content
	 * @throws Exception
	 */
	protected function createFile($path, $content) {
		$base = CONTENT_DIR;
		if (!is_dir($base)) {
			throw new \Exception("Content folder \"$base\" not found.");
		}
		$base .=  ltrim($path, '/');
		file_put_contents($base, $content);
	}

	/**
	 * get random content for page file
	 *
	 * @return string
	 */
	protected function getContent() {
		$content = file_get_contents('http://loripsum.net/api/3/long/headers');
		preg_match('/<h1>(?P<title>.*)<\/h1>/', $content, $matches);
		$title = $matches['title'];
		$content = strip_tags($content);
		$content = <<<EOF
<!--
Title: $title
-->

# $title #

$content
EOF;
		return $content;
	}

}
