<?php

function compressor($files, $name = 'scripts.js', $type = null) {
	return SME\Modules\compressor::make($files, $name, $type);
}