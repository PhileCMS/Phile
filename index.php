<?php
/**
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */

ob_start();

require 'lib/vendor/autoload.php';
require 'config/bootstrap.php';

$app = Phile\Core\Container::getInstance()->get('Phile_App');
$server = new Phile\Http\Server($app);
$request = Zend\Diactoros\ServerRequestFactory::fromGlobals();

try {
    $response = $server->run($request);

    $earlyOutput = ob_get_contents();
    if (!empty($earlyOutput)) {
        return;
    }

    $server->emit($response);
} catch (\Throwable $e) {
    if (\Phile\Core\ServiceLocator::hasService('Phile_ErrorHandler')) {
        ob_end_clean();
        /** @var \Phile\ServiceLocator\ErrorHandlerInterface $errorHandler */
        $errorHandler = \Phile\Core\ServiceLocator::getService('Phile_ErrorHandler');
        $errorHandler->handleException($e);
    } else {
        throw $e;
    }
}
