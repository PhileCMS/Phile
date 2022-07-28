<?php

namespace Phile\Plugin\Phile\PhpFastCache\Tests;

use Phile\Core\Config;
use Phile\Test\TestCase;
use Phile\Core\ServiceLocator;

/**
 * Tests for Plugin class
 *
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\PhpFastCache\Tests
 */
class PluginTest extends TestCase
{
    public function testCreateCacheDefault()
    {
        $this->createPhileCore()->bootstrap();
        $cache = ServiceLocator::getService('Phile_Cache');
        $this->assertInstanceOf(
            \Phile\Plugin\Phile\PhpFastCache\PhileToPsr16CacheAdapter::class,
            $cache
        );
    }

    public function testCreateCacheFile()
    {
        $config = new Config([
            'plugins' => [
                'phile\\phpFastCache' => ['active' => true, 'storage' => 'files']
            ]
        ]);
        $this->createPhileCore(null, $config)->bootstrap();
        $cache = ServiceLocator::getService('Phile_Cache');
        $this->assertInstanceOf(
            \Phile\Plugin\Phile\PhpFastCache\PhileToPsr16CacheAdapter::class,
            $cache
        );
    }

    public function testCreateCacheCustom()
    {
        $customConfig = new \Phpfastcache\Drivers\Memstatic\Config();
        $config = new Config([
            'plugins' => [
                'phile\\phpFastCache' => [
                    'active' => true,
                    'phpFastCacheConfig' => $customConfig
                ]
            ]
        ]);
        $this->createPhileCore(null, $config)->bootstrap();
        $cache = ServiceLocator::getService('Phile_Cache');
        $this->assertInstanceOf(
            \Phile\Plugin\Phile\PhpFastCache\PhileToPsr16CacheAdapter::class,
            $cache
        );
    }
}
