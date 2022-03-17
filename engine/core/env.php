<?php
namespace SME\Core;

use SME\Modules\Cache;

class Env extends Core {

	const cacheName = '__env';

	private static $env, $cache;

	public static function init() {
		if (!file_exists(ROOT.'.env'))
			die('.env not found');
		Config::set('cache', [
			'stores' => [
				'__config' => [
					'driver' => 'file',
					'path' => base_path('storage/.config')
				]
			]
		]);

		self::$cache = Cache::store('__config');
		
		if (self::$cache->has(self::cacheName)) 
			return self::$env = self::$cache->get(self::cacheName);
			
		if ($file = file_get_contents(ROOT.'.env')) {
			$list = explode(PHP_EOL,$file);
			$arrCfg = [];
			foreach($list as $li) {
				if ((isset($li[0]) && $li[0]=='#') || !$li)
					continue;
				$key = NULL;
				$value = NULL;
				$it_li = explode('=',$li);
				if (count($it_li)==2) {
					list($key,$value) = $it_li;
					$value = trim($value);
					$vall = strtolower($value);
					if ($vall=='true' || $vall=='false')
						$value = $vall=='true' ? true : false;
				}
				self::$env[$key] = $value;
			}
		}
		
	}

	public static function __cache() {
		return self::$cache->put(self::cacheName, self::$env);
	}

	public static function __cacheClear() {
		return self::$cache->forget(self::cacheName);
	}

	public static function get($name, $default = '') {
		if (isset(self::$env[$name]))
			return self::$env[$name];
		else
			return $default;
	}
}