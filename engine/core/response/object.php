<?php
namespace SME\Core\Response;

use SME\Core\Response\Objects\Cookie;

class Object {

	private $headers, $content, $code, $cookie;

	public function __construct($params) {
		$this->headers = [];
		$this->content = $params['content'] ?? '';
		$code = $params['code'] ?? 200;
		$this->code($code);
	}

	public function __toString() {
		return (string)$this->getContent();
	}

	public function getContent() {
		if (!is_null($this->cookie))
			($this->cookie)->setCookie();
		return (is_array($this->content) || is_object($this->content)) ? $this->json($this->content) : $this->content;
	}

	public function cookie($name, $value = null, $minutes = 0, $path = '/', $domain = '', $secure = false, $httponly = false) {
		if ($name instanceof Cookie && is_null($value))
			$this->cookie = $name;
		else
			$this->cookie = (new Cookie)($name, $value, $minutes, $path, $domain, $secure, $httponly);

		return $this;
	}

	public function code($code) {
		if (isset($_SERVER['SERVER_PROTOCOL'])) {
			$this->code = $code;
			return $this->header($_SERVER['SERVER_PROTOCOL'].' '.$code);
		}
	}

	public function header(...$header) {
		if (is_array($header)) {
			$headerStr = '';
			if (count($header)==2)
				$headerStr = $header[0].': '.$header[1];
				
			if (count($header)==1)
				$headerStr = $header[0];

			if (!in_array($headerStr, $this->headers)) {
				header($headerStr);
				$this->headers[] = $headerStr;
			}
		}
		return $this;
	}

	public function view() {
		
	}

	public function json() {
		$this->header('Content-Type', 'application/json');
		$this->content = json_encode($this->content, true);
		return $this;
	}
}