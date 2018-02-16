<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Core;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Interop\Http\Factory\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Implements a PSR-15 compatible request handler
 */
class RequestHandler implements RequestHandlerInterface
{
    /** @var array the middlewares to process */
    protected $middleware = [];

    /** @var integer counter */
    protected $current = 0;

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
    }

    /**
     * Adds middleware to the end of the middleware-queue
     *
     * @param MiddlewareInterface $middleware
     */
    public function add(MiddlewareInterface $middleware)
    {
        $this->middleware[] = $middleware;
    }

    /**
     * Adds middleware to the start of the middleware-queue
     *
     * @param MiddlewareInterface $middleware
     */
    public function prepend(MiddlewareInterface $middleware)
    {
        array_unshift($this->middleware, $middleware);
    }

    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request)
    {
        if (!isset($this->middleware[$this->current])) {
            return $this->responseFactory->createResponse();
        }
        $middleware = $this->middleware[$this->current];
        $this->current++;
        return call_user_func_array([$middleware, 'process'], [$request, $this]);
    }
}
