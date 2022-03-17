<?php
namespace SME\Core;

class Config extends Core {
	
	private static $config;

	public static function set(...$vars) {
		if (count($vars)==2) {
			self::$config[$vars[0]] = isset(self::$config[$vars[0]]) ? array_replace_recursive(self::$config[$vars[0]], $vars[1]) : $vars[1];
			return;
		}
	}
	
	public static function get($params) {
		if (empty($params)) {
			return (object)self::$config;
		}
			
		$tempGet = self::$config;
		foreach(explode('.', $params) as $key) {
			if (isset($tempGet[$key]))
				$tempGet = $tempGet[$key];
			else
				return null;
		}
		return $tempGet;	
	}
}

