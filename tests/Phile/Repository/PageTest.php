<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:51
 */

namespace PhileTest\Repository;

use Phile\Core\Container;
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
        $this->pageRepository = new \Phile\Repository\Page();
    }

    /**
     *
     */
    public function testFindByPathSuccess()
    {
        $DS = DIRECTORY_SEPARATOR;

        // official page-Id format
        $tests = [
            '' => 'index.md',
            'index' => 'index.md',
            'sub/' => 'sub' . $DS . 'index.md',
            'sub/page' => 'sub' . $DS . 'page.md',
        ];

        // accept (leading) slashes for backwards compatibility
        $tests += [
            '/' => 'index.md',
            '/index' => 'index.md',
            '/sub/' => 'sub' . $DS . 'index.md',
            '/sub/page' => 'sub' . $DS . 'page.md',
        ];

        foreach ($tests as $pageId => $file) {
            $page = $this->pageRepository->findByPath($pageId);
            $this->assertInstanceOf(
                '\Phile\Model\Page',
                $page,
                "Can't find file '$file' for page-Id: '$pageId'."
            );
            $this->assertStringEndsWith(
                'content' . $DS . $file,
                $page->getFilePath()
            );
        }
    }

    /**
     * Test that global Phile config settings are picked up
     */
    public function testFindByPathPhileConfigSettings()
    {
        //= changed content directory
        $settings['content_dir'] = str_replace('/', DS, __DIR__ . '/../../fixture/content/sub/');
        Container::getInstance()->get('Phile_Config')->merge($settings);

        $repository = new \Phile\Repository\Page();
        $found = $repository->findByPath('c');
        $this->assertInstanceOf(\Phile\Model\Page::class, $found);

        $notFound = $repository->findByPath('d');
        $this->assertNull($notFound);

        // changed content extension
        $settings['content_ext'] = '.markdown';
        Container::getInstance()->get('Phile_Config')->set($settings);

        $repository = new \Phile\Repository\Page();

        $notFound = $repository->findByPath('c');
        $this->assertNull($notFound);

        $found = $repository->findByPath('d');
        $this->assertInstanceOf(\Phile\Model\Page::class, $found);
    }

    /**
     *
     */
    public function testFindByPathPageDoesNotExist()
    {
        $page = $this->pageRepository->findByPath('foo');
        $this->assertNull($page);
    }

    /**
     *
     */
    public function testOrderingFindByMeta()
    {
        // setup
        $titles = ['Sub Page', 'Sub Page Index', 'Welcome'];
        $test = function ($titles, $order) {
            $options = ['pages_order' => $order];
            $pages = $this->pageRepository->findAll($options);
            for ($i = 0; $i < count($pages); $i++) {
                $this->assertEquals($pages[$i]->getTitle(), $titles[$i]);
            }
        };

        // test ascending as default
        $order = 'meta.title';
        $test($titles, $order);

        // test descending
        $order = 'meta.title:desc';
        $titles = array_reverse($titles);
        $test($titles, $order);
    }

    /**
     *
     */
    public function testOrderingInvalidSearchType()
    {
        $message = 'Page order \'meta:title\' was ignored. Type \'\' not recognized.';
        $this->expectException('PHPUnit\Framework\Error\Warning', $message);
        $this->pageRepository
            ->findAll(['pages_order' => 'meta:title'])
            ->toArray();
    }
}
