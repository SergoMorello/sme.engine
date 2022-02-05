<?php

return [
	'CORE' => [
		'core',
		'env',
		'config',
		'database',
		'model/modelCore',
		'model/modelObject',
		'model/modelSql',
		'model/model',
		'model/DB',
		'controller',
		'view/compiler',
		'view/view',
		'response/responseObject',
		'response/response',
		'route/routeInc',
		'route/route',
		'route/console',
		'exceptions',
		'middleware',
		'request',
		'app',
		'log',
	],
	'MODULES' => [
		//Http client
		'httpClient/httpInc',
		'httpClient/http',
		'httpClient/httpResponse',
		'httpClient/httpRequest',

		//Cacher
		'cache/cache',
		'cache/helper',

		//Storage
		'storage/storage',

		//Cookie
		'cookie/cookie',
		'cookie/helper',

		//Session
		'session/session',
		'session/helper',

		//Redirect
		'redirect/redirect',
		'redirect/helper',

		//Compressor
		'compressor/compressor',
		'compressor/helper'
	],
	'HELPERS' => [
		'app',
		'env',
		'config',
		'view',
		'response',
		'dd',
		'route',
		'request',
		'abort',
		'asset',
		'old',
		'log',
	]
];