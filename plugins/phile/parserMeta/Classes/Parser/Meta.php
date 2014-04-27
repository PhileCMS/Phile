<?php
/**
 * The Meta Parser Interface
 */
namespace Phile\Plugin\Phile\ParserMeta\Parser;

use Phile\ServiceLocator\MetaInterface;

/**
 * Class Meta
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMeta\Parser
 */
class Meta implements MetaInterface {
	/** @var array $config the configuration for this parser */
	private $config;

	/**
	 * the constructor
	 *
	 * @param array $config
	 */
	public function __construct(array $config = null) {
		if (!is_null($config)) {
			$this->config = $config;
		}
	}

	/**
	 * parse the content and extract meta informations
	 *
	 * @param $rawData
	 *
	 * @return array with key/value store
	 */
	public function parse($rawData) {
		$rawData = trim($rawData);
		$START   = (substr($rawData, 0, 2) == '/*') ? '/*' : '<!--';
		$END     = (substr($rawData, 0, 2) == '/*') ? '*/' : '-->';

		$metaPart = trim(substr($rawData, strlen($START), strpos($rawData, $END) - (strlen($END) + 1)));
		// split by new lines
		$headers = explode("\n", $metaPart);
		$result  = array();
		foreach ($headers as $line) {
			$parts        = explode(':', $line, 2);
			$key          = preg_replace('/[^\w+]/', '_', strtolower(array_shift($parts))); // replace all special characters with underscores
			$val          = implode($parts);
			$result[$key] = trim($val);
		}

		return $result;
	}
}
