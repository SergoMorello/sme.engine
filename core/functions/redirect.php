<?php
class redirect {
	public function route($name,$props=[]) {
		return $this->rdr(route($name,$props));
	}
	public function rdr($url) {
		header("Location:".$url);
		die();
	}
}
function redirect($url=NULL) {
	$redirect = new redirect;
	if (is_string($url)) {
		return $redirect->rdr($url);
	}elseif (is_null($url))
		return $redirect;
}