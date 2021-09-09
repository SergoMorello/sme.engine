<?php

class config extends core {
	public static function init() {
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
			if (0 === error_reporting())
				return false;
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		});
		
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
}