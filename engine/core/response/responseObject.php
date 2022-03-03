<?php
namespace SME\Core\Response;

class ResponseObject {

	private $headers, $content, $code;

	public function __construct($params) {
		$this->headers = [];
		$this->content = $params['content'] ?? '';
		$code = $params['code'] ?? 200;
		$this->code($code);
	}

	public function __toString() {
		return $this->getContent();
	}

	public function getContent() {
		return (is_array($this->content) || is_object($this->content)) ? $this->json($this->content) : $this->content;
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