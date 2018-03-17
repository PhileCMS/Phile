<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Test;

use Phile\Core\Bootstrap;
use Phile\Core\Config;
use Phile\Core\Container;
use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Utility;
use Phile\Http\Server;
use Phile\Phile;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class for Phile test-cases
 */
abstract class TestCase extends PHPUnitTestCase
{
    /**
     * Creates a Phile-core with bootstrap configuration for testing
     *
     * @param Event $event
     * @param Config $config
     * @return Phile configured core
     */
    protected function createPhileCore(Event $event = null, Config $config = null)
    {
        //# setup Config
        $config = $config ?: new Config;
        if (!$config->has('encryptionKey')) {
            $config->set('encryptionKey', 'testing');
        }
        $testConfig = $config->toArray();

        //# setup container
        Utility::load($config->get('config_dir') . '/container.php');

        $container = Container::getInstance();
        if ($event) {
            $container->set('Phile_EventBus', $event);
        }

        //# setup bootstrap
        $core = $container->get('Phile_App');
        $core->addBootstrap(function ($eventBus, $config) use ($testConfig) {
            $configDir = $config->get('config_dir');
            Bootstrap::loadConfiguration($configDir . 'defaults.php', $config);
            $config->merge($testConfig);

            defined('CONTENT_DIR') || define('CONTENT_DIR', $config->get('content_dir'));
            defined('CONTENT_EXT') || define('CONTENT_EXT', $config->get('content_ext'));

            Event::setInstance($eventBus);

            Bootstrap::loadPlugins($eventBus, $config);

            Registry::set('templateVars', []);
        });

        //# setup middleware
        $core->addMiddleware(function ($middleware, $eventBus, $config) use ($core) {
            $eventBus->trigger('phile.core.middleware.add', ['middleware' => $middleware]);
            $middleware->add($core, 0);
        });
        
        //# additional test setup
        // clears out warnings of inefficient/multiple calls
        \phpFastCache\CacheManager::clearInstances();

        return $core;
    }

    /**
     * Run Phile and create response
     */
    protected function createPhileResponse(Phile $app, ServerRequestInterface $request): ResponseInterface
    {
        $server = new Server($app);
        return $server->run($request);
    }

    /**
     * Creates ServerRequest
     *
     * @param array $server $_SERVER environment
     * @return ServerRequestInterface
     */
    protected function createServerRequestFromArray(array $server = null): ServerRequestInterface
    {
        $server = $server ?: [];
        if (!isset($server['REQUEST_URI'])) {
            $server['REQUEST_URI'] = '/';
        }
        return \Zend\Diactoros\ServerRequestFactory::fromGlobals($server);
    }
}
