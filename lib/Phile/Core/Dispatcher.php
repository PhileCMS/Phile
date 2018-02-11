<?php

namespace Phile\Core;

use Phile\Core\Config;
use Phile\Core\Event;
use Phile\Core\Registry;
use Phile\Core\Response;
use Phile\Core\Router;
use Phile\Core\ServiceLocator;
use Phile\Model\Page;
use Phile\Repository\Page as Repository;

/**
 * Dispatcher class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Dispatcher
{
    
    /** @var Config Phile configuration */
    protected $config;

    /** @var Event event-bus */
    protected $eventBus;

    /**
     * Initializes all core services and plug-ins
     */
    public function __construct(Event $eventBus, Config $config)
    {
        $this->eventBus = $eventBus;
        $this->config = $config;
    }

    /**
     * Processes request into the response
     *
     * Evaluates URL, finds content, renders output
     */
    public function dispatch(Router $router, Response $response)
    {
        $response->setCharset($this->config->get('charset'));

        $this->eventBus->trigger(
            'after_init_core',
            ['router' => $router, 'response' => $response]
        );

        $page = $this->resolveCurrentPage($router, $response);
        $html = $this->renderHtml($page);
        $response->setBody($html);

        return $response;
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
