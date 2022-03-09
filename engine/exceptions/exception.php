<?php
namespace SME\Exceptions;

use SME\Core\Exception as _Exception;

class ExceptionError extends \Exception {
	public function __construct($message, $file = '', $line = 0) {
		$this->message = $message;
		$this->file = $file;
		$this->line = $line;
	}
}

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