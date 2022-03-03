<?php

function redirect($url = null) {
	$redirect = new SME\Modules\Redirect;
	if (is_string($url)) {
		die($redirect->rdr($url));
	}elseif (is_null($url))
		return $redirect;
}