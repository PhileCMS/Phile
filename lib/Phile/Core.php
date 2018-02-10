<?php

namespace Phile;

use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Response;
use Phile\Core\Router;
use Phile\Core\ServiceLocator;
use Phile\Exception\PluginException;
use Phile\Model\Page;
use Phile\Plugin\PluginRepository;
use Phile\Repository\Page as Repository;

/**
 * Phile Core class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Core
{
    /** @var array Phile configuration */
    protected $config;

    /** @var Event event-bus */
    protected $eventBus;

    /**
     * Initializes all core services and plug-ins
     */
    public function initialize()
    {
        $this->setupEventBus()
            ->loadConfiguration()
            ->setupEnvironment()
            ->setupFolders()
            ->loadPlugins()
            ->setupErrorHandler();
        return $this;
    }

    /**
     * Processes request into the response
     *
     * Evaluates URL, finds content, renders output
     */
    public function dispatch(Router $router, Response $response)
    {
        $response->setCharset($this->config['charset']);

        $this->eventBus->trigger('after_init_core', ['response' => $response]);

        $page = $this->resolveCurrentPage($router, $response);
        $html = $this->renderHtml($page);
        $response->setBody($html);
    }

    /**
     * Starts the event system
     */
    protected function setupEventBus()
    {
        $this->eventBus = new Event();
        Event::setInstance($this->eventBus);
        return $this;
    }

    /**
     * Loads core configuration from configuration files
     */
    protected function loadConfiguration()
    {
        $this->config = [];
        $files = [
            'default' => ROOT_DIR . 'default_config.php',
            'local' => ROOT_DIR . 'config.php'
        ];
        foreach ($files as $file) {
            $cfg = include $file;
            $this->config = array_replace_recursive($this->config, $cfg);
        }
        return $this;
    }

    /**
     * Sets additional variables and settings
     */
    protected function setupEnvironment()
    {
        date_default_timezone_set($this->config['timezone']);
        Registry::set('Phile_Settings', $this->config);
        Registry::set('templateVars', []);
        return $this;
    }

    /**
     * Creates and sets up core folders if missing
     */
    protected function setupFolders()
    {
        $dirs = [CACHE_DIR, STORAGE_DIR];
        foreach ($dirs as $dir) {
            if (empty($dir) || strpos($dir, ROOT_DIR) !== 0) {
                continue;
            }
            if (!file_exists($dir)) {
                mkdir($dir, 0775, true);
            }
            $htaccessPath = "$dir.htaccess";
            if (!file_exists($htaccessPath)) {
                $htaccessContent = "order deny,allow\ndeny from all\nallow from 127.0.0.1";
                file_put_contents($htaccessPath, $htaccessContent);
            }
        }
        return $this;
    }

    /**
     * Loads all plug-ins
     *
     * @throws Exception\PluginException
     */
    protected function loadPlugins()
    {
        $pluginsToLoad = $this->config['plugins'];

        $loader = new PluginRepository(PLUGINS_DIR);
        $plugins = $loader->loadAll($pluginsToLoad);
        $errors = $loader->getLoadErrors();

        $this->eventBus->trigger('plugins_loaded', ['plugins' => $plugins]);

        // throw after 'plugins_loaded' so that errorhandler-plugin is available
        if (count($errors) > 0) {
            throw new PluginException($errors[0]['message'], $errors[0]['code']);
        }

        // settings include initialized plugin-configs now
        $this->config = Registry::get('Phile_Settings');
        $this->eventBus->trigger('config_loaded', ['config' => $this->config]);

        return $this;
    }

    /**
     * Initializes error handling
     */
    protected function setupErrorHandler()
    {
        if (!PHILE_CLI_MODE && ServiceLocator::hasService('Phile_ErrorHandler')) {
            $errorHandler = ServiceLocator::getService('Phile_ErrorHandler');
            set_error_handler([$errorHandler, 'handleError']);
            register_shutdown_function([$errorHandler, 'handleShutdown']);
            ini_set('display_errors', $this->config['display_errors']);
        }
        return $this;
    }

    /**
     * Resolves request into the current page
     */
    protected function resolveCurrentPage(Router $router, Response $response)
    {
        $pageId = $router->getCurrentUrl();

        $this->eventBus->trigger('request_uri', ['uri' => $pageId]);

        $repository = new Repository();
        $page = $repository->findByPath($pageId);
        $found = $page instanceof Page;

        if ($found && $pageId !== $page->getPageId()) {
            $url = $router->urlForPage($page->getPageId());
            $response->redirect($url, 301);
        }

        if (!$found) {
            $response->setStatusCode(404);
            $page = $repository->findByPath('404');
            $this->eventBus->trigger('after_404');
        }

        $this->eventBus->trigger('after_resolve_page', ['pageId' => $pageId, 'page' => &$page]);

        return $page;
    }

    /**
     * Renders page into output format (HTML)
     */
    protected function renderHtml(Page $page)
    {
        $this->eventBus->trigger('before_init_template');
        $engine = ServiceLocator::getService('Phile_Template');

        $this->eventBus->trigger(
            'before_render_template',
            ['templateEngine' => &$engine]
        );

        $engine->setCurrentPage($page);
        $html = $engine->render();

        $this->eventBus->trigger(
            'after_render_template',
            ['templateEngine' => &$engine, 'output' => &$html]
        );

        return $html;
    }
}
