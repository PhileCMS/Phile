<?php
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
        'Phile_App'              => Phile\Phile::class,
        'Phile_Config'           => Phile\Core\Config::class,
        'Phile_EventBus'         => Phile\Core\Event::class,
        'Phile_Router'           => Phile\Core\Router::class,

        'Phile_Cache'            => Phile\ServiceLocator\CacheInterface::class,
        'Phile_Template'         => Phile\ServiceLocator\TemplateInterface::class,
        'Phile_Parser'           => Phile\ServiceLocator\ParserInterface::class,
        'Phile_Data_Persistence' => Phile\ServiceLocator\PersistenceInterface::class,
        'Phile_Parser_Meta'      => Phile\ServiceLocator\MetaInterface::class,
        'Phile_ErrorHandler'     => Phile\ServiceLocator\ErrorHandlerInterface::class,
    ]
];

$container = new Phile\Core\Container($config);
Phile\Core\Container::setInstance($container);

$container->set('Phile_EventBus', function () {
    return new Phile\Core\Event;
});

$container->set('Phile_Config', function () {
    return new Phile\Core\Config;
});

$container->set('Phile_App', function ($container) {
    return new Phile\Phile($container->get('Phile_EventBus'), $container->get('Phile_Config'));
});
