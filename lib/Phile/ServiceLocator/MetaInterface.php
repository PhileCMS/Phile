<?php
/**
 * ServiceLocator MetaParser interface
 */
namespace Phile\ServiceLocator;

/**
 * Interface MetaInterface
 *
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\ServiceLocator
 */
interface MetaInterface
{
    /**
     * parse the content
     *
     * @param string $rawData
     *
     * @return array with key/value store
     */
    public function parse($rawData);

    /**
     * Parses text and extracts the content-part (meta-data is removed)
     *
     * @param string $rawData Text to inspect
     * @return string Text without meta-data
     */
    public function extractContent(?string $rawData): string;
}
