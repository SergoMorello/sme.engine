<?php
namespace SME\Core\Response;

class Response {

	public function __call($name, $arg) {
		$response = self::make('');
		if (method_exists($response, $name))
			$response->$name(...$arg);
		else
			throw new \Exception("In response object method \"".$name."\" not found", 1);
	}

	public static function __callStatic($name, $arg) {
		$response = self::make('');
		if (method_exists($response, $name))
			$response->$name(...$arg);
		else
			throw new \Exception("In response object method \"".$name."\" not found", 1);
	}

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
