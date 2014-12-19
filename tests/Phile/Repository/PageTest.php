<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:51
 */

namespace PhileTest\Repository;
use Phile\Model\Page;


/**
 * the PageTest class
 *
 * @author  Frank Nägler
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class PageTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @var \Phile\Repository\Page
	 */
	protected $pageRepository = null;

	/**
	 *
	 */
	protected function setUp() {
		parent::setUp();
		$this->pageRepository = new \Phile\Repository\Page();
	}

	/**
	 *
	 */
	public function testPageCanBeFindByPath() {
		$page = $this->pageRepository->findByPath('/');
		$this->assertInstanceOf('\Phile\Model\Page', $page);
		$this->assertEquals('Welcome', $page->getTitle());

		$page = $this->pageRepository->findByPath('/sub');
		$this->assertInstanceOf('\Phile\Model\Page', $page);
		$this->assertStringEndsWith('sub/index.md', $page->getFilePath());

		$page = $this->pageRepository->findByPath('/sub/page');
		$this->assertInstanceOf('\Phile\Model\Page', $page);
		$this->assertStringEndsWith('sub/page.md', $page->getFilePath());
	}

	/**
	 *
	 */
	public function testPageCanNotBeFoundByPath() {
		$page = $this->pageRepository->findByPath('/foo');
		$this->assertNull($page);
	}

	/**
	 *
	 */
	public function testCanFindAllPagesOrderdByTitle() {
		// @TODO: maybe find a better way to check the correct order
		$titles	= ["Sub Page", "Sub Page Index", "Setup", "Welcome"];
		$pages = $this->pageRepository->findAll(array('pages_order' => 'meta:title'));
		for ($i=0; $i<count($pages); $i++) {
			$this->assertEquals($pages[$i]->getTitle(), $titles[$i]);
		}
	}
}