<?php

return [
	'appName' => env('APP_NAME', 'SME Engine'),

	'debug' => env('APP_DEBUG', true),

	'dbEnabled' => env('DB_ENABLED', false),

	'dbType' => env('DB_TYPE', 'mysql'),

	'dbHost' => env('DB_HOST', '127.0.0.1'),

	'dbUser' => env('DB_USER', 'root'),

	'dbPassword' => env('DB_PASS', ''),

	'dbName' => env('DB_NAME', ''),

	'logEnabled' => env('LOG_ENABLED', false),

	'maxLogSize' => env('MAX_LOG_SIZE', 2097152),

	'compressorEnabled' => env('COMPRESSOR_ENABLED', true),

	'compressorName' => env('COMPRESSOR_NAME', 'com'),
];