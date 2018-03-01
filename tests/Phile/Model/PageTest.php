<?php

namespace PhileTest\Model;

use Phile\Core\Container;
use Phile\Repository\Page as Repository;
use Phile\Test\TestCase;

/**
 * the PageTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class PageTest extends TestCase
{
    /**
     * @var \Phile\Repository\Page
     */
    protected $pageRepository = null;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->createPhileCore()->bootstrap();
        Container::getInstance()->set('Phile_Request', $this->createServerRequestFromArray());
        $this->pageRepository = new Repository();
    }

    /**
     *
     */
    public function testPageHasMetaInformation()
    {
        $this->assertInstanceOf(
            '\Phile\Model\Meta',
            $this->pageRepository->findByPath('/')->getMeta()
        );
    }

    /**
     *
     */
    public function testPageHasTitle()
    {
        $this->assertEquals(
            'Welcome',
            $this->pageRepository->findByPath('/')->getTitle()
        );
    }

    /**
     *
     */
    public function testPageHasContent()
    {
        $this->assertGreaterThan(
            0,
            strlen(
                $this->pageRepository->findByPath('/')
                    ->getContent()
            )
        );
    }

    /**
     *
     */
    public function testPageHasUrl()
    {
        // check if '/index' is stripped correctly
        $result = $this->pageRepository->findByPath('index')->getUrl();
        $this->assertEquals('', $result);

        // root page
        $result = $this->pageRepository->findByPath('404')->getUrl();
        $this->assertEquals('404', $result);

        // sub index
        $result = $this->pageRepository->findByPath('sub/')->getUrl();
        $this->assertEquals('sub/', $result);

        // sub page
        $result = $this->pageRepository->findByPath('sub/page')->getUrl();
        $this->assertEquals('sub/page', $result);
    }

    /**
     *
     */
    public function testPageCanSetContent()
    {
        $page = $this->pageRepository->findByPath('/');
        $page->setContent("testContent");
        $this->assertEquals("<p>testContent</p>\n", $page->getContent());
    }

    /**
     *
     */
    public function testPageGetRawContent()
    {
        $page = $this->pageRepository->findByPath('/');
        $page->setContent('*test*');
        $this->assertEquals('*test*', $page->getRawContent());
    }

    /**
     *
     */
    public function testPageHasMetaObject()
    {
        $page = $this->pageRepository->findByPath('/');
        $this->assertInstanceOf('\Phile\Model\Meta', $page->getMeta());
    }

    /**
     *
     */
    public function testPageHasPreviousPage()
    {
        $page = $this->pageRepository->findByPath('sub/page');
        $this->assertInstanceOf('\Phile\Model\Page', $page->getPreviousPage());
        $this->assertEquals(
            'Sub Page Index',
            $page->getPreviousPage()->getTitle()
        );
    }

    /**
     *
     */
    public function testPageHasNextPage()
    {
        $page = $this->pageRepository->findByPath('index');
        $this->assertInstanceOf('\Phile\Model\Page', $page->getNextPage());
        $this->assertEquals('Sub Page Index', $page->getNextPage()->getTitle());
    }

    public function testRepositoryGet()
    {
        $page = $this->pageRepository->findByPath('index');
        $this->assertInstanceOf(Repository::class, $page->getRepository());
    }

    public function testRepositorySet()
    {
        $page = $this->pageRepository->findByPath('index');

        $repository = new Repository;
        $this->assertNotSame($repository, $page->getRepository());

        $page->setRepository($repository);
        $this->assertSame($repository, $page->getRepository());
    }
}
