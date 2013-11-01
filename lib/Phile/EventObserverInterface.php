<?php

namespace Phile;

interface EventObserverInterface {
	public function on($eventKey, $data = null);
}
