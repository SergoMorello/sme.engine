<?php
namespace SME\Core\Request\Objects;

class Cookie {
	private $__cookie;

	public function __construct($cookie) {
		$this->__cookie = (object)$cookie;
	}

	public function get($name) {
		return $this->__cookie->$name ?? null;
	}

	public function getAll() {
		return $this->__cookie;
	}
}