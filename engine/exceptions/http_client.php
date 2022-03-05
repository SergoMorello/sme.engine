<?php
namespace SME\Exceptions;

class HttpClient extends Exception {
	public function __construct($name) {
		parent::__construct($name);
	}
}