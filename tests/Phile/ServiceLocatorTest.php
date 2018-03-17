<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 20.08.14
 * Time: 16:11
 */

namespace PhileTest;

use PHPUnit\Framework\TestCase;

/**
 * the ServiceLocatorTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class ServiceLocatorTest extends TestCase
{
    /**
     *
     */
    public function testServicePhileCacheExists()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Cache'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileTemplateExists()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Template'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileParserExists()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Parser'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileDatePersistenceExists()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Data_Persistence'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileParserMetaExists()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Parser_Meta'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileErrorHandlerExists()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_ErrorHandler'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileTemplateExistsAndHasCorrectInstance()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Template'
            )
        );
        $this->assertInstanceOf(
            '\Phile\ServiceLocator\TemplateInterface',
            \Phile\Core\ServiceLocator::getService(
                'Phile_Template'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileParserExistsAndHasCorrectInstance()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Parser'
            )
        );
        $this->assertInstanceOf(
            '\Phile\ServiceLocator\ParserInterface',
            \Phile\Core\ServiceLocator::getService(
                'Phile_Parser'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileDatePersistenceExistsAndHasCorrectInstance()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Data_Persistence'
            )
        );
        $this->assertInstanceOf(
            '\Phile\ServiceLocator\PersistenceInterface',
            \Phile\Core\ServiceLocator::getService(
                'Phile_Data_Persistence'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileParserMetaExistsAndHasCorrectInstance()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_Parser_Meta'
            )
        );
        $this->assertInstanceOf(
            '\Phile\ServiceLocator\MetaInterface',
            \Phile\Core\ServiceLocator::getService(
                'Phile_Parser_Meta'
            )
        );
    }

    /**
     *
     */
    public function testServicePhileErrorHandlerExistsAndHasCorrectInstance()
    {
        $this->assertEquals(
            true,
            \Phile\Core\ServiceLocator::hasService(
                'Phile_ErrorHandler'
            )
        );
        $this->assertInstanceOf(
            '\Phile\ServiceLocator\ErrorHandlerInterface',
            \Phile\Core\ServiceLocator::getService(
                'Phile_ErrorHandler'
            )
        );
    }
}
