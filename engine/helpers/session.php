<?php

use SME\Core\Request\Request;

function session($key, $value = null) {
	if (is_string($key) && (is_string($value) || is_callable($value) || is_null($value)))
		return Request::session()->get($key);
	if (is_array($key))
		return Request::session()->put($key);
}