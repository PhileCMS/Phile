<?php
/**
 * @link http://philecms.github.io/
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ErrorHandler
 */

namespace Phile\Plugin\Phile\ErrorHandler;

use Phile\ServiceLocator\ErrorHandlerInterface;
use Whoops\Handler\Handler;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * Developement error handler: use whoops
 */
class Development implements ErrorHandlerInterface
{
    protected $settings;

    protected $whoops;

    public function __construct(array $settings = [])
    {
        $this->settings = $settings;
        $this->whoops = new Run();
        $this->whoops->pushHandler($this->createHandler());
    }

    public function handleError(
        int $errno,
        string $errstr,
        ?string $errfile,
        ?int $errline
    ): bool {
        $level = $this->settings['level'];
        $this->whoops->{Run::ERROR_HANDLER}($level, $errstr, $errfile, $errline);

        return true;
    }

    public function handleException(\Throwable $exception)
    {
        $this->whoops->{Run::EXCEPTION_HANDLER}($exception);
    }

    public function handleShutdown()
    {
        $this->whoops->{Run::SHUTDOWN_HANDLER}();
    }

    protected function createHandler(): Handler
    {
        if (PHILE_CLI_MODE) {
            $handler = new PlainTextHandler;
            $this->whoops->allowQuit(false);
            return $handler;
        }

        $handler = new PrettyPageHandler;
        if (!empty($this->settings['editor'])) {
            $handler->setEditor($this->settings['editor']);
        }
        return $handler;
    }
}
