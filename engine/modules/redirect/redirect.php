<?php
namespace SME\Modules;

use SME\Core\Request\request;

class redirect extends request {
	public static function route($name,$props=[]) {
		return self::rdr(route($name,$props));
	}
	public static function rdr($url) {
		header("Location:".$url);
	}
	public static function back() {
		self::setOldInputs();
		self::rdr(request::server('HTTP_REFERER'));
		return new self;
	}
	public static function withErrors($data) {
		if (!is_array($data))
			return;
		session(['__withErrors'=>$data]);
	}
	private static function setOldInputs() {
		session(['__oldInputs'=>request::all()]);
	}
}
