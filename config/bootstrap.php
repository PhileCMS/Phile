<?php
/**
 * @author PhileCMS
 * @link https://github.com/PhileCMS/Phile
 * @license http://opensource.org/licenses/MIT
 */

/**
 * Set global definitions
 */
// phpcs:disable PSR1.Files.SideEffects
define('PHILE_VERSION', '1.8.0');
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
 * initialize autoloaders
 */
// load classes from Phile-core
spl_autoload_register(function ($className) {
    $fileName = LIB_DIR . str_replace("\\", DS, $className) . '.php';
    if (file_exists($fileName)) {
        require_once $fileName;
    }
});
// load composer installed classes 
require(LIB_DIR . 'vendor' . DS . 'autoload.php');

/**
 * Start global application-object
 */
$app = new \Phile\Phile();

/**
 * Define the bootstrap process
 */
use Phile\Core\Bootstrap;

$app->addBootstrap(function ($eventBus, $config) {
    // Load configuration files into global $config configuration
    $configDir = $config->get('config_dir');
    Bootstrap::loadConfiguration($configDir . 'defaults.php', $config);
    Bootstrap::loadConfiguration($configDir . 'config.php', $config);

    // Setup core folders
    Bootstrap::setupFolder($config->get('cache_dir'), $config);
    Bootstrap::setupFolder($config->get('storage_dir'), $config);

    // Load plug-ins
    Bootstrap::loadPlugins($eventBus, $config);

    // Set error handler
    Bootstrap::setupErrorHandler($config);

    // Set additional PHP environment variables from configuration
    date_default_timezone_set($config->get('timezone'));
});

/**
 * Add PSR-15 middleware
 */
$app->addMiddleware(function ($middleware, $eventBus, $config) use ($app) {
    // add your own (composer installed) middleware here
    // $middleware->add(<new MyMiddleware>);
     
    // Inject middleware from Phile-plugins
    $eventBus->trigger('phile.core.middleware.load', ['middleware' => $middleware]);

    // Add Phile itself as middleware (take request and render output)
    $middleware->add($app);
});
