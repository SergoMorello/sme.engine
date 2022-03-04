<?php
namespace SME\Core\Exceptions;

use SME\Core\Exception as _Exception;

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

class HttpClient extends Exception {
	public function __construct($name) {
		parent::__construct($name);
	}
}

class Console extends Exception {
	public function __construct($name, $errors = []) {
		parent::__construct($name, $errors);
	}
}

class Database extends Exception {}

class Exception extends \Exception {
	private $name, $errors;
	public function __construct($message = '', $errors = []) {
		$this->message = $message;
		$this->errors = $errors;
		$this->code = 1;
		_Exception::throw($this);
	}
	
	public function getErrors() {
		return $this->errors;
	}

	public function getName() {
		return $this->name;
	}
}