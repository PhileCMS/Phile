<?php
/**
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */

require_once __DIR__ . '/lib/Phile/bootstrap.php';

ob_start();

try {
    $router = new \Phile\Core\Router();
    $response = new \Phile\Core\Response();
    (new \Phile\Core())
        ->initialize()
        ->dispatch($router, $response);
    $response->send();
} catch (\Exception $e) {
    if (\Phile\Core\ServiceLocator::hasService('Phile_ErrorHandler')) {
        ob_end_clean();

        /** @var \Phile\ServiceLocator\ErrorHandlerInterface $errorHandler */
        $errorHandler = \Phile\Core\ServiceLocator::getService(
            'Phile_ErrorHandler'
        );
        $errorHandler->handleException($e);
    } else {
        throw $e;
    }
}
