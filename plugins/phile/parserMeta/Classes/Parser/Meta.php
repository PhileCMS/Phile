<?php
/**
 * The Meta Parser Interface
 */
namespace Phile\Plugin\Phile\ParserMeta\Parser;

use Phile\ServiceLocator\MetaInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Meta
 *
 * @author  PhileCMS
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
	 * parse the content and extract meta information
	 *
	 * @param string $rawData raw page data
	 *
	 * @return array with key/value store
	 */
	public function parse($rawData) {
		$rawData = trim($rawData);
		$START   = (substr($rawData, 0, 2) == '/*') ? '/*' : '<!--';
		$END     = (substr($rawData, 0, 2) == '/*') ? '*/' : '-->';

		$meta = trim(substr($rawData, strlen($START), strpos($rawData, $END) - (strlen($END) + 1)));
		$meta = Yaml::parse($meta);
		$meta = $this->convertKeys($meta);
		return $meta;
	}

	/**
	 * convert meta data keys
	 *
	 * Creates "compatible" keys allowing easy access e.g. as template var.
	 *
	 * Conversions applied:
	 *
	 * - lowercase all chars
	 * - replace special chars and whitespace with underscore
	 *
	 * @param array $meta meta-data
	 * @return array
	 */
	protected function convertKeys(array $meta) {
		$return = [];
		foreach ($meta as $key => $value) {
			if (is_array($value)) {
				$value = $this->convertKeys($value);
			}
			$newKey = strtolower($key);
			$newKey = preg_replace('/[^\w+]/', '_', $newKey);
			$return[$newKey] = $value;
		}
		return $return;
	}
}
