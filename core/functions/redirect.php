<?php
class redirect extends request {
	public function route($name,$props=[]) {
		return $this->rdr(route($name,$props));
	}
	public static function rdr($url) {
		header("Location:".$url);
	}
	public static function back() {
		self::setOldInputs();
		self::rdr($_SERVER['HTTP_REFERER']);
		return new self;
	}
	public static function withErrors($data) {
		if (!is_array($data))
			return;
		session(['__withErrors'=>$data]);
	}
	private static function setOldInputs() {
		session(['__oldInputs'=>request::POST()]);
	}
}
function redirect($url=NULL) {
	$redirect = new redirect;
	if (is_string($url)) {
		die($redirect->rdr($url));
	}elseif (is_null($url))
		return $redirect;
}