<?php
/**
 * The EventObserverInterface
 */
namespace Phile\Gateway;

/**
 * Interface EventObserverInterface
 *
 * @author  Frank Nägler
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Gateway
 */
interface EventObserverInterface
{
    /**
     * event method
     *
     * @param string $eventKey
     * @param mixed  $data
     *
     * @return mixed
     */
    public function on($eventKey, $data = null);
}
