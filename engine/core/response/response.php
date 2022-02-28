<?php
namespace SME\Core\Response;

class Response {
	public static function make($content, $code = 200) {
		$response = new ResponseObject([
			'content' => $content,
			'code' => $code
		]);
		return $response;
	}
	
	public static function json($content, $code = 200) {
		return self::make($content, $code)->json();
	}
}
