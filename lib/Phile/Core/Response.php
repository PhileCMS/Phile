<?php

  namespace Phile\Core;

  /**
   * Response class implements HTTP response handling
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

    public function setBody($body) {
      $this->body = $body;
      return $this;
    }

    public function setCharset($charset) {
      $this->charset = $charset;
      return $this;
    }

    public function setHeader($key, $value) {
      $this->headers[] = "$key: $value";
      return $this;
    }

    public function setStatusCode($code) {
      $this->statusCode = $code;
      return $this;
    }

    public function send() {
      $this->setHeader('Content-Type', 'text/html; charset=' . $this->charset);
      $this->_outputHeader();
      http_response_code($this->statusCode);
      echo $this->body;
    }

    protected function _outputHeader(){
      foreach($this->headers as $header) {
        header($header);
      }
    }

  }

