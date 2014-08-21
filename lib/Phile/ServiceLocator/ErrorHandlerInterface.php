<?php
/**
 * The ErrorHandlerInterface
 */
namespace Phile\ServiceLocator;

/**
 * Interface ErrorHandlerInterface
 *
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\ServiceLocator
 */
interface ErrorHandlerInterface {
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
	public function handleError($errno, $errstr, $errfile, $errline, array $errcontext);

	/**
	 * handle all exceptions
	 *
	 * @param \Exception $exception
	 *
	 * @return mixed
	 */
	public function handleException(\Exception $exception);
}
