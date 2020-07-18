<?php
/**
 * Bootstraps the Phile-core
 *
 * There's no need to make changes here in an ordinary Phile installation.
 * It allows advanced configuration options if necessary.
 *
 * @author PhileCMS
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 */

/**
 * Set global definitions
 */
// phpcs:disable PSR1.Files.SideEffects
define('PHILE_VERSION', '1.11.1');
define('PHILE_CLI_MODE', (php_sapi_name() === 'cli'));
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', realpath(__DIR__ . DS . '..' . DS) . DS);
define('LIB_DIR', ROOT_DIR . 'lib' . DS);
define('CONFIG_DIR', ROOT_DIR . DS . 'config' . DS);
define('PLUGINS_DIR', ROOT_DIR . 'plugins' . DS);
define('THEMES_DIR', ROOT_DIR . 'themes' . DS);
define('CACHE_DIR', LIB_DIR . 'cache' . DS);
define('STORAGE_DIR', LIB_DIR . 'datastorage' . DS);
// phpcs:enable

/**
 * Setup container
 */
require 'container.php';
$container = Phile\Core\Container::getInstance();

/**
 * Register plugin directories
 *
 * Allows autoloading from plugin-directories and early usage in config.php
 *
 * @var \Phile\Plugin\PluginRepository $plugins
 */
$plugins = $container->get('Phile_Plugins');
$plugins->addDirectory(PLUGINS_DIR);

/**
 * Setup global application-object
 *
 * @var \Phile\Phile $app
 */
$app = $container->get('Phile_App');

/**
 * Define the bootstrap process
 */
use Phile\Core\Bootstrap;
use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Registry;

$app->addBootstrap(function (Event $eventBus, Config $config) use ($plugins): void {
    // Load configuration files into global $config configuration
    $configDir = $config->get('config_dir');
    Bootstrap::loadConfiguration($configDir . 'defaults.php', $config);
    Bootstrap::loadConfiguration($configDir . 'config.php', $config);

    // backwards-compatibility for deprecated global constants set in Config now
    // phpcs:disable PSR1.Files.SideEffects
    defined('CONTENT_DIR') || define('CONTENT_DIR', $config->get('content_dir'));
    defined('CONTENT_EXT') || define('CONTENT_EXT', $config->get('content_ext'));
    // phpcs:enable

    // Setup core folders
    Bootstrap::setupFolder($config->get('cache_dir'), $config);
    Bootstrap::setupFolder($config->get('storage_dir'), $config);

    // backwards-compatibility for deprecated static Event access
    Event::setInstance($eventBus);

    // Load plug-ins
    $plugins->load($config);

    // Set error handler
    Bootstrap::setupErrorHandler($config);

    // Intialize global registry objects
    Registry::set('templateVars', []);

    // Set additional PHP environment variables from configuration
    date_default_timezone_set($config->get('timezone'));
});

/**
 * Add PSR-15 middleware
 */
use Phile\Http\MiddlewareQueue;

$app->addMiddleware(function (MiddlewareQueue $middleware, Event $eventBus, Config $config) use ($app): void {
    // Inject middleware from Phile-plugins
    $eventBus->trigger('phile.core.middleware.add', ['middleware' => $middleware]);

    // Add Phile itself as middleware (take request and render output)
    $middleware->add($app);
});
