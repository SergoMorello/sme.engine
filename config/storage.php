<?php
// Only local driver
return [
	'default' => 'local',
	
	'disks' => [

		'local' => [
			'driver' => 'local',
			'root' => storage_path('.local')
		]
	]
];