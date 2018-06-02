<?php
/**
 * The ServiceLocator interface
 */
namespace Phile\ServiceLocator;

/**
 * Interface ParserInterface
 *
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\ServiceLocator
 */
interface ParserInterface
{
    /**
     * parse data
     *
     * @param string $data
     *
     * @return mixed
     */
    public function parse($data);
}
