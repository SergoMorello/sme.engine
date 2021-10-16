<?php

function redirect($url=NULL) {
	$redirect = new redirect;
	if (is_string($url)) {
		die($redirect->rdr($url));
	}elseif (is_null($url))
		return $redirect;
}