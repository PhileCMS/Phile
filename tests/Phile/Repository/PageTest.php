<?php
/**
 * Created by PhpStorm.
 * User: franae
 * Date: 21.08.14
 * Time: 23:51
 */

namespace PhileTest\Repository;


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
	}

	/**
	 *
	 */
	public function testCanFindAllPagesOrderdByTitle() {
		$this->markTestIncomplete('not implemented yet');
	}
}