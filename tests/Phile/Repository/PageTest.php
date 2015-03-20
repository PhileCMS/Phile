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
	public function testFindByPathSuccess() {
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
			'/' => 'index.md' ,
			'/index' => 'index.md' ,
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
			$this->assertStringEndsWith('content' . $DS . $file, $page->getFilePath());
		}
	}

	/**
	 *
	 */
	public function testFindByPathPageDoesNotExist() {
		$page = $this->pageRepository->findByPath('foo');
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
