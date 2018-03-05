<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Http;

use Phile\Phile;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\SapiEmitter;

/**
 * Runs Phile as standalone application
 */
class Server
{
    /** @var Phile app to run */
    protected $app;
    
    public function __construct(Phile $app)
    {
        $this->app = $app;
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $this->app->bootstrap();
        $middleware = $this->app->middleware(new MiddlewareQueue());
        $requestHandler = new RequestHandler($middleware, new ResponseFactory);
        return $requestHandler->handle($request);
    }

    public function emit(ResponseInterface $response)
    {
        (new SapiEmitter)->emit($response);
    }
}
