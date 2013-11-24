<?php

namespace Phile\Model;
use Phile\Event;
use Symfony\Component\Yaml\Parser;

/**
 * Meta model
 *
 * @author Frank Nägler
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */
class Meta extends AbstractModel {
	public function __construct($rawData = null) {
		if ($rawData !== null) {
			$this->setRawData($rawData);
		}
	}

	public function setRawData($rawData) {
		/**
		 * @triggerEvent before_read_file_meta this event is triggered before the meta data readed and parsed
		 * @param string rawData the unparsed data
		 * @param \Phile\Model\Meta meta the meta model
		 */
		Event::triggerEvent('before_read_file_meta', array('rawData' => &$rawData, 'meta' => &$this));
		$this->parseRawData($rawData);
		/**
		 * @triggerEvent after_read_file_meta this event is triggered after the meta data readed and parsed
		 * @param string rawData the unparsed data
		 * @param \Phile\Model\Meta meta the meta model
		 */
		Event::triggerEvent('after_read_file_meta', array('rawData' => &$rawData, 'meta' => &$this));
	}

	public function getFormattedDate() {
		global $config;
		if (isset($this->data['date'])) {
			return date($config['date_format'], strtotime($this->data['date']));
		}
		return null;
	}

	protected function parseRawData($rawData) {
		$rawData = trim($rawData);
		$START  = (substr($rawData, 0, 2) == '/*') ? '/*' : '<!--';
		$END    = (substr($rawData, 0, 2) == '/*') ? '*/' : '-->';

		$metaPart   = trim(substr($rawData, strlen($START), strpos($rawData, $END)-(strlen($END)+1)));

		$yamlParser = new Parser();
	    $headers = array_change_key_case($yamlParser->parse($metaPart));
	    $this->data = $headers;
		
	}
}
