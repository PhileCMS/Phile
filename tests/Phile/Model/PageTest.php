<?php


class PageTest extends PHPUnit_Framework_TestCase {

	public function testPageHasMetaInformation() {
		$repository = new \Phile\Repository\Page();
		$page = $repository->findByPath('/');
		$this->assertInstanceOf('\Phile\Model\Meta', $page->getMeta());
	}

	public function testPageHasTitle() {
		$repository = new \Phile\Repository\Page();
		$page = $repository->findByPath('/');
		$this->assertEquals('Welcome', $page->getTitle());
	}
}