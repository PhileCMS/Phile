<?php

/**
 * @author Frank Nägler
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */

require_once __DIR__ . '/lib/Phile/Bootstrap.php';

ob_start();

try {
	$boostrap = \Phile\Bootstrap::getInstance()->initializeBasics();
	$phileCore = new \Phile\Core($boostrap);
	echo $phileCore->render();
} catch (\Phile\Exception\AbstractException $e) {
	if (\Phile\Core\ServiceLocator::hasService('Phile_ErrorHandler')) {
		ob_end_clean();

		/** @var \Phile\ServiceLocator\ErrorHandlerInterface $errorHandler */
		$errorHandler = \Phile\Core\ServiceLocator::getService('Phile_ErrorHandler');
		$errorHandler->handleException($e);
	}
} catch (\Exception $e) {
	if (\Phile\Core\ServiceLocator::hasService('Phile_ErrorHandler')) {
		ob_end_clean();

		/** @var \Phile\ServiceLocator\ErrorHandlerInterface $errorHandler */
		$errorHandler = \Phile\Core\ServiceLocator::getService('Phile_ErrorHandler');
		$errorHandler->handleException($e);
	}
}
