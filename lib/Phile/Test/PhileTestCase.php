<?php

namespace Phile\Test;

use Phile\Core\ServiceLocator;

abstract class PhileTestCase extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
		$this->setupCache();
	}

	protected function setupCache() {
		/** @var \Phile\ServiceLocator\CacheInterface $cache */
		ServiceLocator::registerService('Phile_Cache', new NullCache());
	}

}

