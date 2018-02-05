<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:51
 */

namespace PhileTest\Repository;

use Phile\Core\Registry;
use PHPUnit\Framework\TestCase;

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
        $this->pageRepository = new \Phile\Repository\Page();
        $this->phileSettings = Registry::get('Phile_Settings');
    }

    protected function tearDown()
    {
        Registry::set('Phile_Settings', $this->phileSettings);
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
        $settings = $this->phileSettings;
        $settings['content_dir'] = PLUGINS_DIR . str_replace('/', DS, 'phile/testPlugin/content/sub/');
        Registry::set('Phile_Settings', $settings);

        $repository = new \Phile\Repository\Page();
        $found = $repository->findByPath('c');
        $this->assertInstanceOf(\Phile\Model\Page::class, $found);

        $notFound = $repository->findByPath('d');
        $this->assertNull($notFound);

        // changed content extension
        $settings['content_ext'] = '.markdown';
        Registry::set('Phile_Settings', $settings);

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
        if (class_exists('PHPUnit\Framework\Error\Warning')) {
            // PHPUnit 6
            $this->expectException('PHPUnit\Framework\Error\Warning', $message);
        } else {
            // PHPUnit 5
            $this->expectException('PHPUnit_Framework_Error_Warning', $message);
        }
        $this->pageRepository
            ->findAll(['pages_order' => 'meta:title'])
            ->toArray();
    }
}
