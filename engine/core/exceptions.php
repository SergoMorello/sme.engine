<?php
namespace SME\Core\Exceptions;

use SME\Core\Exception;

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

class Validate extends Exception {
	public function __construct($errors) {
		parent::__construct('validation', $errors);
	}
}