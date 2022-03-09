<?php
namespace SME\Exceptions;

class Http extends Exception {
	private $httpCode;
	public function __construct($name, $code) {
		$this->httpCode = $code;
		parent::__construct($name, $code);
	}
	public function getHttpCode() {
		return $this->httpCode;
	}
}