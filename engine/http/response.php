<?php
namespace SME\Http;

use SME\Core\Response\Response as ResponseCore;

class Response extends ResponseCore {

	public function __invoke($content, $code = 200) {
		return self::make($content, $code);
	}
}