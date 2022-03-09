<?php
namespace SME\Modules;

use SME\Core\Request\Request;
use SME\Core\Response\Response;

class Redirect extends Request {

	public static function route($name,$props=[]) {
		return self::rdr(route($name,$props));
	}

	public static function rdr($url) {
		return Response::header('Location', $url);
	}

	public static function back() {
		self::setOldInputs();
		self::rdr(Request::server('HTTP_REFERER'));
		return new self;
	}

	public static function withErrors($data) {
		if (!is_array($data))
			return;
		session(['__withErrors' => $data]);
	}

	private static function setOldInputs() {
		session(['__oldInputs' => Request::all()]);
	}
}
