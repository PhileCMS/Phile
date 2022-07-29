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
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMeta\Parser
 */
class Meta implements MetaInterface
{
    /**
     * @var array $config the configuration for this parser
     */
    private $config;

    /**
     * the constructor
     *
     * @param array $config
     */
    public function __construct(array $config = null)
    {
        if (!is_null($config)) {
            $this->config = $config;
        }
    }

    /**
     * Implements MetaInterface::parse
     *
     * {@inheritdoc}
     */
    public function extractMeta($rawData)
    {
        list($meta) = $this->splitRawIntoMetaAndContent($rawData);
        
        if ($meta === null) {
            return [];
        }

        if (strtolower($this->config['format']) === 'yaml') {
            $meta = Yaml::parse($meta);
        } else {
            $meta = $this->parsePhileFormat($meta);
        }
        $meta = ($meta === null) ? [] : $this->convertKeys($meta);
        return $meta;
    }

    /**
     * Implements MetaInterface::extractContent
     *
     * {@inheritdoc}
     */
    public function extractContent(?string $rawData): string
    {
        list(, $content) = $this->splitRawIntoMetaAndContent($rawData);
        
        return $content;
    }

    /**
     * Inspects the text and splits meta-data and content
     *
     * @param string $rawData Text to inspect
     * @return array array with [meta-data, content]
     */
    protected function splitRawIntoMetaAndContent(string $rawData): array
    {
        $meta = null;
        $content = $rawData;

        if ($rawData !== null) {
            $rawData = trim($rawData);
            $fences = $this->config['fences'];
            foreach ($fences as $fence) {
                if (strncmp($fence['open'], $rawData, strlen($fence['open'])) === 0) {
                    $sub = substr($rawData, strlen($fence['open']));
                    list($meta, $content) = explode($fence['close'], $sub, 2);
                    break;
                }
            }
        }

        return [$meta, $content];
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
     * @param  array $meta meta-data
     * @return array
     */
    protected function convertKeys(array $meta)
    {
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

    /**
     * Phile meta format parser.
     *
     * @param  string $string unparsed meta-data
     * @return array|null array with meta-tags; null: on meta-data found
     *
     * @deprecated since 1.6.0 Phile is going to switch to YAML
     */
    protected function parsePhileFormat($string)
    {
        if (empty($string)) {
            return null;
        }
        $meta = [];
        $lines = explode("\n", $string);
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }
            $parts = array_map('trim', $parts);
            $meta[$parts[0]] = $parts[1];
        }
        return $meta;
    }
}
