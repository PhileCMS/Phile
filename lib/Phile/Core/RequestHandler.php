<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Core;

use Interop\Http\Factory\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Implements a PSR-15 compatible request handler
 */
class RequestHandler implements RequestHandlerInterface
{
    /** @var \SplPriorityQueue the middleware to process */
    protected $middleware;

    /** @var ResponseFactoryInterface PSR-17 HTTP response factory */
    protected $responseFactory;

    /**
     * Constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->middleware = new \SplPriorityQueue();
    }

    /**
     * Adds middleware to the the middleware-queue
     *
     * @param MiddlewareInterface $middleware Middleware to add
     * @param int $priority Priority orders middleware in queue
     */
    public function add(MiddlewareInterface $middleware, $priority = 10)
    {
        $this->middleware->insert($middleware, $priority);
    }

    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->middleware->key() === 0) {
            $this->middleware->rewind();
        }
        if (!$this->middleware->valid()) {
            return $this->responseFactory->createResponse();
        }
        $current = $this->middleware->current();
        $this->middleware->next();
        return call_user_func_array([$current, 'process'], [$request, $this]);
    }
}
