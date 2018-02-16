<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\RequestHandler;
use Phile\Core\Response;
use Phile\Core\Router;
use Phile\Model\Page;
use Phile\Repository\Page as Repository;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Phile Core class
 */
class Phile implements MiddlewareInterface
{
    /** @var Config Phile configuration */
    protected $config;

    /** @var Event event-bus */
    protected $eventBus;

    /** @var array callbacks run at bootstrap */
    protected $bootstrapConfigs = [];

    /** @var array callbacks run on middleware-setup */
    protected $middlewareConfigs = [];

    /**
     * Constructor sets-up base Phile environment
     *
     * @param Event $eventBus
     * @param Config $config
     */
    public function __construct(Event $eventBus = null, Config $config = null)
    {
        $this->eventBus = $eventBus ?: new Event;
        $this->config = $config ?: new Config;

        Registry::set('Phile.Core.EventBus', $this->eventBus);
        Registry::set('Phile.Core.Config', $this->config);
        Registry::set('templateVars', []);

        // provides backwards-compatibility for deprecated static Event access
        Event::setInstance($this->eventBus);
    }

    /**
     * Adds bootstrap-setup
     */
    public function addBootstrap(callable $bootstrap)
    {
        $this->bootstrapConfigs[] = $bootstrap;
        return $this;
    }

    /**
     * Adds middleware-setup
     */
    public function addMiddleware(callable $middleware)
    {
        $this->middlewareConfigs[] = $middleware;
        return $this;
    }

    /**
     * Performs bootstrap
     */
    public function bootstrap()
    {
        foreach ($this->bootstrapConfigs as $config) {
            call_user_func_array($config, [$this->eventBus, $this->config]);
        }
        return $this;
    }

    /**
     * Processes request
     */
    public function dispatch($request)
    {
        $this->bootstrap();
        $this->config->lock();

        $requestHandler = new RequestHandler(new Response);
        foreach ($this->middlewareConfigs as $config) {
            call_user_func_array($config, [$requestHandler, $this->eventBus, $this->config]);
        }

        return $requestHandler->handle($request);
    }

    /**
     * Implements PSR-15 middle-ware process-handler
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $this->eventBus->trigger('after_init_core');

        $router = new Router();
        $pageId = $router->getCurrentUrl();
        $page = $this->resolveCurrentPage($pageId, $router);

        if ($page instanceof ResponseInterface) {
            return $page;
        }

        $html = $this->renderHtml($page);

        $charset = $this->config->get('charset');
        $response = (new Response)
            ->createHtmlResponse($html)
            ->withHeader('Content-Type', 'text/html; charset=' . $charset);

        if ($page->getPageId() == '404') {
            $response = $response->withStatus(404) ;
        }

        return $response;
    }

    /**
     * Resolves request into the current page
     */
    protected function resolveCurrentPage($pageId, Router $router)
    {
        $repository = new Repository();
        $page = $repository->findByPath($pageId);
        $found = $page instanceof Page;

        if ($found && $pageId !== $page->getPageId()) {
            $url = $router->urlForPage($page->getPageId());
            return (new Response)->createRedirectResponse($url, 301);
        }

        if (!$found) {
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

        $coreVars = $this->config->getTemplateVars();
        $templateVars = Registry::get('templateVars') + $coreVars;

        $this->eventBus->trigger(
            'before_render_template',
            ['templateEngine' => &$engine, 'templateVars' => &$templateVars]
        );

        Registry::set('templateVars', $templateVars);

        $engine->setCurrentPage($page);
        $html = $engine->render();

        $this->eventBus->trigger(
            'after_render_template',
            ['templateEngine' => &$engine, 'output' => &$html]
        );

        return $html;
    }
}
