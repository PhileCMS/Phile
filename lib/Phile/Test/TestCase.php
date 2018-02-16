<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Test;

use Phile\Core\Config;
use Phile\Core\Bootstrap;
use Phile\Core\Event;
use Phile\Phile;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\ServerRequestFactory;

/**
 * Class for Phile test-cases
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Creates a Phile-core with bootstrap configuration for testing
     */
    protected function getBootstrappedCore(Event $event = null, Config $config = null)
    {
        $core = new Phile($event, $config);
        $core->addBootstrap(function ($eventBus, $config) {
            $configDir = $config->get('config_dir');
            Bootstrap::loadConfiguration($configDir . 'defaults.php', $config);
            Bootstrap::loadPlugins($eventBus, $config);
        });
        $core->addMiddleware(function ($middleware, $eventBus, $config) use ($core) {
            $middleware->add($core);
        });
        return $core;
    }

    /**
     * Creates a response from a Phile-core
     */
    protected function dispatchCore(Phile $core, RequestInterface $request = null)
    {
        $request = $request ?: ServerRequestFactory::fromGlobals();
        return $core->dispatch($request);
    }
}
