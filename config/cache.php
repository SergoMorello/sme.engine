<?php
// only file driver
return [
	'default' => 'file',

	'stores' => [

		'file' => [
			'driver' => 'file',
			'path' => base_path('storage/.cache')
		],

	]
];