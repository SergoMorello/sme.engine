<?php

function asset($path) {
	if (!is_string($path))
		return;
	return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/'.$path;
}