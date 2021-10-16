<?php

class config extends core {
	
	public static function set(...$vars) {
		if (count($vars)==2) {
			core::$arrConfig[$vars[0]] = $vars[1];
			return;
		}
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
				core::$arrConfig[$key] = $value;
			}
		}
	}
	
	public static function get($param) {
		if (isset(core::$arrConfig[$param]))
			return core::$arrConfig[$param];
	}
}

