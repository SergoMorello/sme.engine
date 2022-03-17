<?php

function base_path($path = null) {
	if (!is_string($path))
		return ROOT;
	return is_null($path) ? ROOT : ROOT.$path.'/';
}

function storage_path($path = null) {
	if (!is_string($path))
		return STORAGE;
	return is_null($path) ? STORAGE : STORAGE.$path.'/';
}