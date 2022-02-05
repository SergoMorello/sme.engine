<?php

class env extends core {

	private static $env;

	public static function init() {
		if (!file_exists(ROOT.'.env'))
			die('.env not found');
		
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

	public static function get($name, $default = '') {
		if (isset(self::$env[$name]))
			return self::$env[$name];
		else
			return $default;
	}
}