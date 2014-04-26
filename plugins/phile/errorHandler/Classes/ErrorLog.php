<?php
/**
 * The Error Handler
 */

namespace Phile\Plugin\Phile\ErrorHandler;
use Phile\ServiceLocator\ErrorHandlerInterface;

/**
 * Class ErrorLog
 */
class ErrorLog implements ErrorHandlerInterface {
	/**
	 * handle the error
	 *
	 * @param int    $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int    $errline
	 * @param array  $errcontext
	 *
	 * @return boolean
	 */
	public function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
		error_log("[{$errno}] {$errstr} in {$errfile} on line {$errline}");
	}

	/**
	 * handle all exceptions
	 *
	 * @param \Exception $exception
	 *
	 * @return mixed
	 */
	public function handleException(\Exception $exception) {
		error_log("[{$exception->getCode()}] {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}");
	}
}