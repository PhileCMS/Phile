<?php
/**
 * @link http://philecms.github.io/
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ErrorHandler
 */

namespace Phile\Plugin\Phile\ErrorHandler;

use Phile\ServiceLocator\ErrorHandlerInterface;

/**
 * Error handler logging to files
 */
class ErrorLog implements ErrorHandlerInterface
{
    /**
     * Constructor
     *
     * @param string|null $logfile path to error.log-file; null: PHP default error log
     */
    public function __construct(?string $logfile = null)
    {
        if ($logfile) {
            ini_set('error_log', $logfile);
        }
    }

    public function handleError(
        int $errno,
        string $errstr,
        ?string $errfile,
        ?int $errline
    ): bool {
        $this->log($errno, $errstr, $errfile, $errline);

        return true;
    }

    public function handleException(\Throwable $exception)
    {
        $code = (int)$exception->getCode();
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $this->log($code, $message, $file, $line);
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        exit;
    }

    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error === null) {
            return;
        }
        $this->log($error['type'], $error['message'], $error['file'], $error['line']);
    }

    protected function log(int $code, string $message, ?string $file, ?int $line): void
    {
        error_log("[{$code}] {$message} in {$file} on line {$line}");
    }
}
