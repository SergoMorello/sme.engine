<?php

function base_path($path = null) {
	if (!is_string($path))
		return ROOT;
	return is_null($path) ? ROOT : ROOT.$path.'/';
}