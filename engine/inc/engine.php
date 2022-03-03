<?php

return [
	'CORE' => [
		'core',
		'env',
		'config',
		'model/database',
		'model/DB',
		'model/modelCore',
		'model/modelObject',
		'model/modelSql',
		'model/modelMethods',
		'model/model',
		'controllerCore',
		'view/compiler',
		'view/view',
		'response/responseObject',
		'response/response',
		'route/routeCore',
		'route/route',
		'route/console',
		'exception',
		'middleware',
		'request/validateIs',
		'request/validate',
		'request/objects/file',
		'request/objects/files',
		'request/request',
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
		'base_path',
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
		'is'
	]
];