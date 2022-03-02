<?php

use SME\Modules\Cookie;

function cookie($data = null, $time = 0) {
	if (is_null($data))
		return new Cookie;
	if (is_array($data)) {
		return Cookie::make($data, $time);
	}elseif(is_string($data)){
		return Cookie::get($data);
	}
}