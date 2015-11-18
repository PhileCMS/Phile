<?php

namespace PhileTest;

/**
 * tests class for encryption hash generator PHP script
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 * @deprecated since 2015-05-01, will be removed together with generator.php
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerator()
    {
        // output length should be exactly 64 chars
        $string = preg_quote('<br><br><br>');
        $this->expectOutputRegex('/^.{64}' . $string . '.*$/');

        include ROOT_DIR . '/generator.php';
    }
}
