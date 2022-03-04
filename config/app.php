<?php

return [
	'appName' => env('APP_NAME', 'SME Engine'),

	'debug' => env('APP_DEBUG', true),

	'logEnabled' => env('LOG_ENABLED', false),

	'maxLogSize' => env('MAX_LOG_SIZE', 2097152),

	'compressorEnabled' => env('COMPRESSOR_ENABLED', true),

	'compressorName' => env('COMPRESSOR_NAME', 'com'),

	'locale' => 'en',

	'fallback_locale' => 'en'
];