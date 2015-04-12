<?php

namespace PhileTest;

use Phile\Core;
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

		$settings = Registry::get('Phile_Settings');
		Registry::set('Phile_Settings', ['base_url' => $baseUrl] + $settings);

		$redirects = [
			'sub' => 'sub/',
			'sub/page/' => 'sub/page'
		];

		foreach ($redirects as $current => $expected) {
			$Core = $this->getMockBuilder('Phile\Core')
				->setMethods([
					'checkSetup',
					'initializeErrorHandling',
					'initializeTemplate'
				])
				->disableOriginalConstructor()
				->getMock();

			$response = $this->getMock(
				'\Phile\Core\Response',
				['redirect', 'stop']
			);
			$router = new Router(['REQUEST_URI' => $current]);

			$response->expects($this->once())
				->method('redirect')
				->with($baseUrl . '/' . $expected, 301);

			$Core->__construct($router, $response);
		}
	}

	/**
	 * tests redirect to setup page if setup is unfinished
	 */
	public function testCheckSetupRedirectToSetupPage() {
		$settings = Registry::get('Phile_Settings');
		Registry::set('Phile_Settings', ['encryptionKey' => ''] + $settings);

		$_SERVER['REQUEST_URI'] = '/';
		$response = $this->getMock('\Phile\Core\Response', ['redirect']);
		$router = new Router();
		$response->expects($this->once())
			->method('redirect')
			->with($router->url('setup'));

		new Core($router, $response);
	}
}
