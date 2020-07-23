<?php
/**
 * The ErrorHandlerInterface
 */
namespace Phile\ServiceLocator;

/**
 * Interface ErrorHandlerInterface
 *
 * @link    https://philecms.github.io
 * @license http://opensource.org/licenses/MIT
 * @package Phile\ServiceLocator
 */
interface ErrorHandlerInterface
{
    /**
     * Handle the error
     *
     * @param int $errno
     * @param string $errstr
     * @param string|null $errfile
     * @param int|null $errline
     * @return bool True if error was handled successfully, false otherwise.
     */
    public function handleError(
        int $errno,
        string $errstr,
        ?string $errfile,
        ?int $errline
    ): bool;

    /**
     * Handle all exceptions
     *
     * @param \Throwable $exception
     *
     * @return mixed
     */
    public function handleException(\Throwable $exception);

    /**
     * Handle shutdown
     */
    public function handleShutdown();
}
