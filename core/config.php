<?php

class config extends core {
	public static function init() {
		core::setConfig('APP_NAME','SME Engine');
		
		core::setConfig('APP_DEBUG',true);
		
		core::setConfig('DB_ENABLED',false);
		
		core::setConfig('DB_TYPE','mysql');
		
		core::setConfig('DB_HOST','127.0.0.1');
		
		core::setConfig('DB_USER','');
		
		core::setConfig('DB_PASS','');
		
		core::setConfig('DB_NAME','');
		
		core::$arrStorages = [
			[
			'name'=>'local',
			'path'=>'.local',
			'default'=>true
			]
		];
		
		core::setConfig();
	}
	public static function get($param) {
		if (isset(core::$arrConfig[$param]))
			return core::$arrConfig[$param];
	}
}

function config($param='') {
	if ($param)
		return config::get($param);
	else
		return new class($param) {
			public function __construct() {
				if (count(core::$arrConfig))
					foreach(core::$arrConfig as $key=>$value)
						$this->$key = $value;
			}
		};
}