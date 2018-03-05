<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Http;

use Interop\Http\Factory\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Implements a PSR-15 compatible request handler
 */
class RequestHandler implements RequestHandlerInterface
{
    /** @var Iterator the middleware to process */
    protected $queue;

    /** @var ResponseFactoryInterface PSR-17 HTTP response factory */
    protected $responseFactory;

    /** @var integer level */
    protected $level = 0;

    /**
     * Constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(MiddlewareQueue $queue, ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->queue = $queue->getIterator();
    }

    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->level++ === 0) {
            $this->queue->rewind();
        }
        $current = $this->queue->current();
        if (!$current) {
            return $this->responseFactory->createResponse();
        }
        $this->queue->next();
        $result = call_user_func_array([$current, 'process'], [$request, $this]);
        $this->level--;
        return $result;
    }
}
