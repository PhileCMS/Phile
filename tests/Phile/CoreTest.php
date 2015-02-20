<?php

namespace PhileTest;

use Phile\Core\Registry;
use Phile\Core\Router;

/**
 * the CoreTest class
 *
 * @author  Phile CMS
 * @link    https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package PhileTest
 */
class CoreTest extends \PHPUnit_Framework_TestCase {

	/**
	 *
	 */
	public function testInitializeCurrentPageTidyUrlRedirect() {
		$baseUrl = 'http://foo';
		$currentUrl = 'sub/';
		$tidyUrl = 'sub';

		$settings = Registry::get('Phile_Settings');
		Registry::set('Phile_Settings', ['base_url' => $baseUrl] + $settings);

		$Core = $this->getMockBuilder('Phile\Core')
			->setMethods([
				'checkSetup',
				'initializeErrorHandling',
				'initializeTemplate'
			])
			->disableOriginalConstructor()
			->getMock();

		$_SERVER['REQUEST_URI'] = $currentUrl;
		$response = $this->getMock(
			'\Phile\Core\Response',
			['redirect', 'setStatusCode', 'stop']
		);
		$response
			->expects($this->once())
			->method('setStatusCode')
			->with(301)
			->will($this->returnValue($response));
		$response
			->expects($this->once())
			->method('redirect')
			->with($baseUrl . '/' . $tidyUrl);

		$Core->__construct($response);
	}

	/**
	 * tests redirect to setup page if setup is unfinished
	 */
	public function testCheckSetupRedirectToSetupPage() {
		$settings = Registry::get('Phile_Settings');
		Registry::set('Phile_Settings', ['encryptionKey' => ''] + $settings);

		$_SERVER['REQUEST_URI'] = '/';
		$response = $this->getMock('\Phile\Core\Response', ['redirect']);
		$response->expects($this->once())
			->method('redirect')
			->with(Router::url('setup'));

		new \Phile\Core($response);
	}
}
