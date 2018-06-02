<?php

use Phile\Core\Config;
use Phile\Core\Container;
use Phile\Core\Event;
use Phile\Core\Router;
use Phile\Phile;
use Phile\Plugin\PluginRepository;

/**
 * Setup global Container with dependencies
 *
 * There's no need to make changes here in an ordinary Phile installation.
 * It allows advanced configuration options if necessary.
 *
 * @author PhileCMS
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 */

$config = [
    'types' => [
        'Phile_App'              => Phile::class,
        'Phile_Config'           => Config::class,
        'Phile_EventBus'         => Event::class,
        'Phile_Plugins'          => PluginRepository::class,
        'Phile_Router'           => Router::class,

        'Phile_Cache'            => \Phile\ServiceLocator\CacheInterface::class,
        'Phile_Template'         => \Phile\ServiceLocator\TemplateInterface::class,
        'Phile_Parser'           => \Phile\ServiceLocator\ParserInterface::class,
        'Phile_Data_Persistence' => \Phile\ServiceLocator\PersistenceInterface::class,
        'Phile_Parser_Meta'      => \Phile\ServiceLocator\MetaInterface::class,
        'Phile_ErrorHandler'     => \Phile\ServiceLocator\ErrorHandlerInterface::class,
    ]
];

$container = new Container($config);
Container::setInstance($container);

$container->set('Phile_EventBus', function (): Event {
    return new Event;
});

$container->set('Phile_Config', function (): Config {
    return new Config;
});

$container->set('Phile_App', function (Container $container): Phile {
    return new Phile($container->get('Phile_EventBus'), $container->get('Phile_Config'));
});

$container->set('Phile_Plugins', function (Container $container): PluginRepository {
    return new PluginRepository($container->get('Phile_EventBus'));
});
