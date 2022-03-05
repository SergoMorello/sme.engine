<?php
namespace SME\Exceptions;

class Validate extends Exception {
	private $validateErrors;
	public function __construct($errors, $errorMessages) {
		$this->validateErrors = $errorMessages;
		parent::__construct('validation', $errors);
	}

	public function getMessageErrors() {
		return $this->validateErrors;
	}
}