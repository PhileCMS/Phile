<?php
/**
 * @link http://philecms.github.io/
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ErrorHandler\Test
 */

namespace Phile\Plugin\Phile\ErrorHandler\Tests;

use Phile\Core\Bootstrap;
use Phile\Core\Config;
use Phile\Test\TestCase;
use ReflectionFunction;

class PluginTest extends TestCase
{
    /**
     * Basic test that whoops plugin is running.
     *
     * The exception is not thrown but caught by Whoops and rendered plaintext
     * CLI response.
     */
    public function testWhoops()
    {
        $config = new Config([
            'plugins' => [
                'phile\\errorHandler' => ['active' => true, 'handler' => 'development']
            ]
        ]);
        $eventBus = new \Phile\Core\Event;
        $eventBus->register('after_init_core', function () {
            throw new \Exception('1845F098-9035-4D8E-9E31');
        });

        $request = $this->createServerRequestFromArray();

        $body = null;
        try {
            $core = $this->createPhileCore($eventBus, $config);
            $core->addBootstrap(function ($eventBus, $config) {
                $config->set('phile_cli_mode', false);
                Bootstrap::setupErrorHandler($config);
            });
            $this->createPhileResponse($core, $request);
        } catch (\Exception $e) {
            ob_start();
            $errorHandler = \Phile\Core\ServiceLocator::getService(
                'Phile_ErrorHandler'
            );
            $errorHandler->handleException($e);
            $body = ob_get_clean();
        }

        $this->assertStringStartsWith('Exception: 1845F098-9035-4D8E-9E31 in file', $body);

        $handler = new ReflectionFunction(set_exception_handler(null));
        $this->assertInstanceOf(
            \Phile\Plugin\Phile\ErrorHandler\Development::class,
            $handler->getClosureThis()
        );
    }
}
