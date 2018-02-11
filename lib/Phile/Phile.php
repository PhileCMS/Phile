<?php

namespace Phile;

use Phile\Core\BaseSetup;
use Phile\Core\Config;
use Phile\Core\Dispatcher;
use Phile\Core\Event;
use Phile\Core\Response;
use Phile\Core\Router;

/**
 * Phile Core class
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Phile
{
    /** @var Config Phile configuration */
    protected $config;

    /** @var Event event-bus */
    protected $eventBus;

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

        BaseSetup::setUp($this->eventBus, $this->config);
    }

    /**
     * Takes request and creates response.
     *
     * @param Router $router
     * @param Response $response
     * @return Response
     */
    public function dispatch(Router $router = null, Response $response = null)
    {
        $router = $router ?: new Router();
        $response = $response ?: new Response();

        $this->config->lock();

        $dispatcher = new Dispatcher($this->eventBus, $this->config);
        $response = $dispatcher->dispatch($router, $response);

        return $response;
    }
}
