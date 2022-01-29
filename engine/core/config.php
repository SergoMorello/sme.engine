<?php

class config extends core {
	
	private static $config;

	public static function set(...$vars) {
		if (count($vars)==2) {
			self::$config[$vars[0]] = $vars[1];
			return;
		}
		
	}
	
	public static function get($params) {
		if (empty($params)) {
			return (object)self::$config;
		}
			
		$p = explode('.', $params);
		
		return (count($p) > 1) ? self::$config[$p[0]][$p[1]] : self::$config[$p[0]];
			
	}
}

