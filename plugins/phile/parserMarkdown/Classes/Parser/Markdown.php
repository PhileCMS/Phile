<?php
/**
 * The Mardown parser class
 */
namespace Phile\Plugin\Phile\ParserMarkdown\Parser;

use Michelf\MarkdownExtra;
use Phile\ServiceLocator\ParserInterface;

/**
 * Class Markdown
 *
 * @author  Frank Nägler
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ParserMarkdown\Parser
 */
class Markdown implements ParserInterface
{
    /**
     * @var mixed the configuration
     */
    private $config;

    /**
     * the constructor
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * overload parse with the MarkdownExtra parser
     *
     * @param string $data
     *
     * @return string
     */
    public function parse($data)
    {
        $parser = new MarkdownExtra;
        foreach ($this->config as $key => $value) {
            if (property_exists($parser, $key)) {
                $parser->{$key} = $value;
            }
        }

        return $parser->transform($data);
    }
}
