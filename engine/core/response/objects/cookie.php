<?php
namespace SME\Core\Response\Objects;

class Cookie {
	private $cookie;

	public function __construct() {
		$this->cookie = [];
	}

	public function __invoke(...$args) {
		return $this->queue(...$args);
	}

	public function queue(...$args) {
		if (!isset($args[0], $args[1]))
			return;
		$this->cookie[] = (object)[
			'name' => $args[0],
			'value' => $args[1],
			'minutes' => $args[2] ?? 0,
			'path' => $args[3] ?? '/',
			'domain' => $args[4] ?? '',
			'secure' => $secure[5] ?? false,
			'httponly' => $httponly[6] ?? false
		];
		return $this;
	}

	public function getCookie() {
		return $this->cookie;
	}

	public function setCookie() {
		foreach($this->cookie as $cookie)
			setcookie(
				$cookie->name,
				$cookie->value,
				$cookie->minutes,
				$cookie->path,
				$cookie->domain,
				$cookie->secure,
				$cookie->httponly
			);
	}
}