<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 */

namespace Phile\Http;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response;

/**
 * Creates PSR-7 responses
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function createResponse($code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response('php://memory', $code);
    }

    /**
     * Creates PSR-7 HTML response
     */
    public function createHtmlResponse(string $body, int $code = 200): ResponseInterface
    {
        return new HtmlResponse($body, $code);
    }

    /**
     * Creates PSR-7 redirect response
     */
    public function createRedirectResponse(string $url, int $code = 302): ResponseInterface
    {
        return new RedirectResponse($url, $code);
    }
}
