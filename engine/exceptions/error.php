<?php
namespace SME\Exceptions;

class Error extends Exception {
	public function __construct($message, $code = 0, $file = '', $line = 0, $showCode = true) {
		$this->message = $message;
		$this->code = $code;
		$this->file = $file;
		$this->line = $line;
		$this->showCode = $showCode;
	}
}