<?php
/*
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 */

namespace PhileTest;

use Phile\Core\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConfigLockedError()
    {
        $config = new Config();
        $config->lock();

        $this->expectException(\LogicException::class);
        $this->expectExceptionCode(1518440759);

        $config->set('foo', 'bar');
    }
}
