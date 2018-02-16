<?php
/**
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */

try {
    ob_start();
    require_once __DIR__ . '/config/bootstrap.php';

    $request = Zend\Diactoros\ServerRequestFactory::fromGlobals();
    $response = $app->dispatch($request);

    $earlyOutput = ob_get_contents();
    if (!empty($earlyOutput)) {
        return;
    }
    $emiter = new \Zend\Diactoros\Response\SapiEmitter();
    $emiter->emit($response);
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
