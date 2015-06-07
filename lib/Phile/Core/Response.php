<?php

/**
 * the Response class
 */

namespace Phile\Core;

/**
 * the Response class is responsible for sending a HTTP response to the client
 *
 * Response is chainable and can be used anywhere:
 *
 * 	(new Respose)->setBody('Hello World')->send();
 *
 * After send() Phile is terminated.
 *
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile
 */
class Response {

	/**
	 * @var string HTTP body
	 */
	protected $body = '';

	/**
	 * @var string charset
	 */
	protected $charset = 'utf-8';

	/**
	 * @var array HTTP-headers
	 */
	protected $headers = [];

	/**
	 * @var int HTTP status code
	 */
	protected $statusCode = 200;

	/**
	 * redirect to another URL
	 *
	 * @param string $url URL
	 * @param int $statusCode
	 */
	public function redirect($url, $statusCode = 302) {
		$this->setStatusCode($statusCode)
			->setHeader('Location', $url, true)
			->setBody('')
			->send()
			->stop();
	}

	/**
	 * set the response body
	 *
	 * @param $body
	 * @return $this
	 */
	public function setBody($body) {
		$this->body = $body;
		return $this;
	}

	/**
	 * set the response character-set
	 *
	 * @param $charset
	 * @return $this
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}

	/**
	 * set a response HTTP-header
	 *
	 * @param string $key
	 * @param string $value
	 * @param bool $clear clear out any existing headers
	 * @return $this
	 */
	public function setHeader($key, $value, $clear = false) {
		if ($clear) {
			$this->headers = [];
		}
		$this->headers[$key] = "$key: $value";
		return $this;
	}

	/**
	 * set the response HTTP status code
	 *
	 * @param $code
	 * @return $this
	 */
	public function setStatusCode($code) {
		$this->statusCode = $code;
		return $this;
	}

	/**
	 * sends the HTTP response
	 *
	 * @return $this
	 */
	public function send() {
		if (!isset($this->headers['Content-Type'])) {
			$this->setHeader('Content-Type', 'text/html; charset=' . $this->charset);
		}
		$this->outputHeader();
		http_response_code($this->statusCode);
		echo $this->body;
		return $this;
	}

	/**
	 * helper for easy testing
	 */
	public function stop() {
		die();
	}

	/**
	 * output all set response headers
	 */
	protected function outputHeader() {
		foreach ($this->headers as $header) {
			header($header);
		}
	}

}

