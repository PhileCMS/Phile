<?php
/**
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */

require_once __DIR__ . '/bootstrap.php';

ob_start();

try {
    $core = new \Phile\Phile;
    $response = $core->dispatch();
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
