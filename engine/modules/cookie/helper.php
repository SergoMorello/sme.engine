<?php

function cookie($data = null, $time = 0) {
	if (is_null($data))
		return new cookie;
	if (is_array($data)) {
		return cookie::make($data, $time);
	}elseif(is_string($data)){
		return cookie::get($data);
	}
}