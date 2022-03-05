<?php
namespace SME\Exceptions;

class Console extends Exception {
	public function __construct($name, $errors = []) {
		parent::__construct($name, $errors);
	}
}