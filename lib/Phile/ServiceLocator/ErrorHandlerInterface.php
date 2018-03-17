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
interface ErrorHandlerInterface
{
    /**
     * handle the error
     *
     * @param int $errno
     * @param string $errstr
     * @param string|null $errfile
     * @param int|null $errline
     *
     * @return bool
     */
    public function handleError(int $errno, string $errstr, ?string $errfile, ?string $errline);

    /**
     * handle all exceptions
     *
     * @param \Throwable $exception
     *
     * @return mixed
     */
    public function handleException(\Throwable $exception);

    /**
     * handle shutdown
     */
    public function handleShutdown();
}
