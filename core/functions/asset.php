<?php

function asset($path) {
	if (!is_string($path))
		return;
	return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].'/'.$path;
}