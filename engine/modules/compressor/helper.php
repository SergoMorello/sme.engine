<?php

function compressor($files, $name = 'scripts.js', $type = null) {
	return compressor::make($files, $name, $type);
}