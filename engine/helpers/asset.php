<?php

function asset($path) {
	if (!is_string($path))
		return;
	$path = $path[0] == '/' ? substr($path, 1) : $path;
	return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/'.$path;
}