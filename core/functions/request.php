<?php
class request {
	protected static function POST() {
		return core::guardData($_POST);
	}
	protected static function GET() {
		return core::guardData($_GET);
	}
	public function route($var) {
		if (is_string($var))
			return route::$props[$var];
	}
	public function data($var=NULL) {
		$GET = self::GET();
		if (is_string($var))
			return $GET[$var];
		elseif (is_null($var))
			return (object)$GET;
	}
	public function input($var) {
		if (is_string($var))
			return self::POST()[$var];
	}
}
function request() {
	return new request;
}