<?php

class config extends core {
	public static function init() {
		set_error_handler(function($errno, $errstr, $errfile, $errline) {
			if (0 === error_reporting())
				return false;
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		});
		
		self::set('APP_NAME','SME Engine');
		
		self::set('APP_DEBUG','true');
		
		self::set('DB_ENABLED','false');
		
		self::set('DB_TYPE','mysql');
		
		self::set('DB_HOST','127.0.0.1');
		
		self::set('DB_USER','');
		
		self::set('DB_PASS','');
		
		self::set('DB_NAME','');
		
		core::$arrStorages = [
			[
			'name'=>'local',
			'path'=>'.local',
			'default'=>true
			]
		];
		
		self::set();
	}
	public static function set(...$vars) {
		if (count($vars)==2) {
			core::$arrConfig[$vars[0]] = $vars[1];
			return;
		}
		if ($file = file_get_contents(ROOT.'.env')) {
			$list = explode(PHP_EOL,$file);
			$arrCfg = [];
			foreach($list as $li) {
				if ((isset($li[0]) && $li[0]=='#') || !$li)
					continue;
				$liEx = explode('=',$li);
				$liEx[1] = trim($liEx[1]);
				if ($liEx[1]=='true')
					$liEx[1] = true;
				else
				if ($liEx[1]=='false')
					$liEx[1] = false;
				core::$arrConfig[$liEx[0]] = $liEx[1];
			}
		}else
			die('.env not found');
	}
}