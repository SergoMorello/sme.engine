<?php

class storage extends core {
	static $props=[];
	public static function disk($name="") {
		if (!empty($name))
			self::$props['disk'] = $name;
		return (new storage);
	}
	private static function getDisk($name="") {
		$name = empty($name) ? self::$props['disk'] ?? NULL : $name;
		
		foreach(core::$arrStorages as $disk) {
			if (empty($name))
				if (isset($disk['default']) && $disk['default'])
					return (object)$disk;
			if ($disk['name']==$name)
				return (object)$disk;
		}
	}
	public static function put($name,$data) {
		return file_put_contents(ROOT.'storage/'.self::getDisk()->path.'/'.$name,$data);
	}
	public static function get($name) {
		return file_get_contents(ROOT.'storage/'.self::getDisk()->path.'/'.$name);
	}
	public static function exists($name) {
		return file_exists(ROOT.'storage/'.self::getDisk()->path.'/'.$name);
	}
	public static function delete($name) {
		$names = is_array($name) ? $name : [$name];
		foreach($names as $name)
			if (!unlink(ROOT.'storage/'.self::getDisk()->path.'/'.$name))
				return false;
		return true;
	}
}