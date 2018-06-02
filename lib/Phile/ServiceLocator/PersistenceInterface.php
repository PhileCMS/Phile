<?php
/**
 * The Persistence ServiceLocator interface
 */
namespace Phile\ServiceLocator;

/**
 * Interface PersistenceInterface
 *
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\ServiceLocator
 */
interface PersistenceInterface
{
    /**
     * check if an entry exists for given key
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * get the entry by given key
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * set the value for given key
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * delete the entry by given key
     *
     * @param string $key
     */
    public function delete($key);
}
