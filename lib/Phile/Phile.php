<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile;

use Phile\Core\Config;
use Phile\Core\Container;
use Phile\Core\Event;
use Phile\Core\Response;
use Phile\Core\Router;
use Phile\Http\MiddlewareQueue;
use Phile\Model\Page;
use Phile\Repository\Page as Repository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

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
     */
    public function __construct(Event $eventBus, Config $config)
    {
        $this->eventBus = $eventBus;
        $this->config = $config;
    }

    /**
     * Adds bootstrap-setup
     */
    public function addBootstrap(callable $bootstrap): self
    {
        $this->bootstrapConfigs[] = $bootstrap;
        return $this;
    }

    /**
     * Adds middleware-setup
     */
    public function addMiddleware(callable $middleware): self
    {
        $this->middlewareConfigs[] = $middleware;
        return $this;
    }

    /**
     * Performs bootstrap
     */
    public function bootstrap(): self
    {
        foreach ($this->bootstrapConfigs as $config) {
            call_user_func_array($config, [$this->eventBus, $this->config]);
        }
        return $this;
    }

    /**
     * Populates Phile controlled middle-ware-queue
     */
    public function middleware(MiddlewareQueue $queue): MiddlewareQueue
    {
        foreach ($this->middlewareConfigs as $config) {
            call_user_func_array($config, [$queue, $this->eventBus, $this->config]);
        }
        return $queue;
    }

    /**
     * Run a request through Phile and create a response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->config->lock();

        $router = new Router($request->getServerParams());
        Container::getInstance()->set('Phile_Router', $router);

        $response = $this->triggerEventWithResponse('after_init_core');
        if ($response) {
            return $response;
        }

        $page = $this->resolveCurrentPage($router);
        if ($page instanceof ResponseInterface) {
            return $page;
        }

        $notFound = $page->getPageId() == $this->config->get('not_found_page');
        if ($notFound && !$this->config->get('handle_not_found')) {
            return $handler->handle($request);
        }

        $html = $this->renderHtml($page);
        if ($html instanceof ResponseInterface) {
            return $html;
        }

        return $this->createResponse($html, $notFound ? 404 : 200);
    }

    /**
     * Resolves request into the current page
     *
     * @return Page|ResponseInterface
     */
    protected function resolveCurrentPage(Router $router)
    {
        $pageId = $router->getCurrentUrl();
        $response = $this->triggerEventWithResponse('request_uri', ['uri' => $pageId]);
        if ($response) {
            return $response;
        }

        $repository = new Repository();
        $page = $repository->findByPath($pageId);
        $found = $page instanceof Page;

        if ($found && $pageId !== $page->getPageId()) {
            $url = $router->urlForPage($page->getPageId());
            return (new Response)->createRedirectResponse($url, 301);
        }

        if (!$found) {
            $page = $repository->findByPath($this->config->get('not_found_page'));
            $this->eventBus->trigger('after_404');
        }

        $response = $this->triggerEventWithResponse(
            'after_resolve_page',
            ['pageId' => $pageId, 'page' => &$page]
        );
        if ($response) {
            return $response;
        }

        return $page;
    }

    /**
     * Renders page into output format (HTML)
     *
     * @return string|ResponseInterface
     */
    protected function renderHtml(Page $page)
    {
        $this->eventBus->trigger('before_init_template');
        $engine = ServiceLocator::getService('Phile_Template');

        $coreVars = $this->config->getTemplateVars();
        $templateVars = Registry::get('templateVars') + $coreVars;
        Registry::set('templateVars', $templateVars);

        $response = $this->triggerEventWithResponse(
            'before_render_template',
            ['templateEngine' => &$engine]
        );
        if ($response) {
            return $response;
        }

        $engine->setCurrentPage($page);
        $html = $engine->render();

        $this->eventBus->trigger(
            'after_render_template',
            ['templateEngine' => &$engine, 'output' => &$html]
        );

        return $html;
    }

    /**
     * Creates response
     */
    protected function createResponse(string $output, int $status): ResponseInterface
    {
        $charset = $this->config->get('charset');
        $response = (new Response)
            ->createHtmlResponse($output)
            ->withHeader('Content-Type', 'text/html; charset=' . $charset)
            ->withStatus($status);
        return $this->triggerEventWithResponse('after_response_created', ['response' => &$response]);
    }

    /**
     * Triggers event injecting a response parameter and returning it if set
     */
    protected function triggerEventWithResponse(string $eventName, array $eventData = []): ?ResponseInterface
    {
        if (empty($eventData['response'])) {
            $response = null;
            $eventData = array_merge(['response' => &$response], $eventData);
        }
        $this->eventBus->trigger($eventName, $eventData);
        return $eventData['response'];
    }
}
