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
use Phile\Phile;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
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

        //# setup container
        Utility::load($config->get('config_dir') . '/container.php');

        $container = Container::getInstance();
        if ($event) {
            $container->set('Phile_EventBus', $event);
        }

        $container->set('Phile_Config', $config);

        //# setup bootstrap
        $core = $container->get('Phile_App');
        $core->addBootstrap(function ($eventBus, $config) {
            $configDir = $config->get('config_dir');
            Bootstrap::loadConfiguration($configDir . 'defaults.php', $config);

            defined('CONTENT_DIR') || define('CONTENT_DIR', $config->get('content_dir'));
            defined('CONTENT_EXT') || define('CONTENT_EXT', $config->get('content_ext'));

            Bootstrap::loadPlugins($eventBus, $config);

            Registry::set('templateVars', []);

            Event::setInstance($eventBus);
        });

        //# setup middleware
        $core->addMiddleware(function ($middleware, $eventBus, $config) use ($core) {
            $eventBus->trigger('phile.core.middleware.add', ['middleware' => $middleware]);
            $middleware->add($core, 0);
        });

        return $core;
    }

    /**
     * Creates ServerRequest
     *
     * @param array $server $_SERVER environment
     * @return ServerRequestInterface
     */
    protected function createServerRequestFromArray($server = null)
    {
        return \Zend\Diactoros\ServerRequestFactory::fromGlobals($server);
    }
}
