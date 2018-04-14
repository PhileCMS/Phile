<?php
/**
 * @author  PhileCMS
 * @link    https://philecms.github.io/
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */

namespace PhileTest\Plugin;

use Phile\Exception\PluginException;
use Phile\Plugin\PluginDirectory;
use PHPUnit\Framework\TestCase;

class PluginDirectoryTest extends TestCase
{
    public function testFailureNotPluginAbstract()
    {
        $directory = new PluginDirectory(__DIR__ . '/../../fixture/plugins/');

        $this->expectException(PluginException::class);
        $this->expectExceptionCode(1398536526);

        $directory->newPluginInstance('phile\testPluginNotPluginAbstract');
    }
}
