<?php
/**
 * Model class
 */
namespace Phile\Model;

use Phile\Core\Container;
use Phile\Core\ServiceLocator;

/**
 * Meta model
 *
 * @author  Frank Nägler
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Model
 */
class Meta extends AbstractModel
{

    /**
     * the construtor
     *
     * @param string $rawData the raw data to parse
     */
    public function __construct($rawData = null)
    {
        if ($rawData !== null) {
            $this->setRawData($rawData);
        }
    }

    /**
     * set the raw data to parse
     *
     * @param string $rawData the raw data
     */
    public function setRawData($rawData)
    {
        /**
         * @triggerEvent before_read_file_meta this event is triggered before the meta data readed and parsed
         *
         * @param string rawData the unparsed data
         * @param \Phile\Model\Meta meta   the meta model
         */
        Container::getInstance()->get('Phile_EventBus')->trigger(
            'before_read_file_meta',
            ['rawData' => &$rawData, 'meta' => &$this]
        );
        $this->parseRawData($rawData);
        /**
         * @triggerEvent after_read_file_meta this event is triggered after the meta data readed and parsed
         *
         * @param string rawData the unparsed data
         * @param \Phile\Model\Meta meta   the meta model
         */
        Container::getInstance()->get('Phile_EventBus')->trigger(
            'after_read_file_meta',
            ['rawData' => &$rawData, 'meta' => &$this]
        );
    }

    /**
     * get formatted date
     *
     * @return bool|null|string
     */
    public function getFormattedDate()
    {
        $config = Container::getInstance()->get('Phile_Config');
        if (!isset($this->data['date'])) {
            return null;
        }
        $date = $this->data['date'];
        if (!is_numeric($date)) {
            $date = strtotime($date);
        }
        return date($config->get('date_format'), $date);
    }

    /**
     * parse the raw data
     *
     * @param string $rawData
     */
    protected function parseRawData($rawData)
    {
        /**
         * @var \Phile\ServiceLocator\MetaInterface $metaParser
         */
        $metaParser = ServiceLocator::getService('Phile_Parser_Meta');
        $data       = $metaParser->extractMeta($rawData);

        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }
}
