<?php
namespace SME\Http;

use SME\Core\Response\Objects\Cookie as ResponseCookie;

class Request extends \SME\Core\Request\Request {}

class Response extends \SME\Core\Response\Response {}

class Cookie {

	private $responseCookie;

	public function __construct() {
		$this->responseCookie = new ResponseCookie;
	}

	public static function queue($name, $value, $minutes = 0) {
		if (isset($this))
			return $this->responseCookie->queue($name, $value, $minutes);
		return (new ResponseCookie)($name, $value, $minutes);
	}

	public static function get($name = null) {
		return Request::cookie($name);
	}
}