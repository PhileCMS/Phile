<?php

/**
 * tests class for encryption hash generator PHP script
 *
 * @author  PhileCMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase {

  public function testGenerator() {
    // output length should be exactly 64 chars
    $this->expectOutputRegex('/^.{64}$/');

    include ROOT_DIR . '/generator.php';
  }

}
