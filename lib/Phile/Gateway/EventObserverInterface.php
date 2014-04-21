<?php

namespace Phile\Gateway;

/**
 * Interface EventObserverInterface
 *
 * @package Phile\Gateway
 */
interface EventObserverInterface {
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
