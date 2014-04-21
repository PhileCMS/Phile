<?php
namespace Phile\Parser;

use Phile\ServiceLocator\MetaInterface;

class Meta implements MetaInterface {
	/** @var array $config the configuration for this parser */
	private $config;

	/**
	 * @param array $config
	 */
	public function __construct(array $config = null) {
		if (!is_null($config)) {
			$this->config = $config;
		}
	}

	/**
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
			$key          = strtolower(array_shift($parts));
			$val          = implode($parts);
			$result[$key] = trim($val);
		}

		return $result;
	}
}
