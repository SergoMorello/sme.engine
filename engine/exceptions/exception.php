<?php
namespace SME\Exceptions;

use SME\Core\Exception as _Exception;

class Exception extends \Exception {
	protected $name, $errors;
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