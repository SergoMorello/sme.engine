<?php

function path($path = null, $root = ROOT) {
	if (!is_string($path))
		return $root;
	return is_null($path) ? $root : $root.$path.'/';
}

function base_path($path = null) {
	return path($path, ROOT);
}

function storage_path($path = null) {
	return path($path, STORAGE);
}

function app_path($path = null) {
	return path($path, APP);
}