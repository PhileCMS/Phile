<?php
/**
 * The Error Handler
 */

namespace Phile\Plugin\Phile\ErrorHandler;

use Phile\ServiceLocator\ErrorHandlerInterface;

/**
 * Class ErrorLog
 */
class ErrorLog implements ErrorHandlerInterface
{
    /**
     * handle the error
     *
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     *
     * @return boolean
     */
    public function handleError(int $errno, string $errstr, ?string $errfile, ?string $errline)
    {
        $this->log($errno, $errstr, $errfile, $errline);
    }

    /**
     * handle all exceptions
     *
     * @param \Exception $exception
     *
     * @return mixed
     */
    public function handleException(\Throwable $exception)
    {
        $code = (int)$exception->getCode();
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $this->log($code, $message, $file, $line);
    }

    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        $this->log($error['type'], $error['message'], $error['file'], $error['line']);
    }

    protected function log(int $code, string $message, ?string $file, ?string $line): void
    {
        error_log("[{$code}] {$message} in {$file} on line {$line}");
    }
}
