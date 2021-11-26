<?php

class storage extends core {
	
	private static $props=[];

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
	private static function makeFolders($path) {
		$arrFolders = explode('/',$path);
		if (count($arrFolders)<=1)
			return;
			
		array_pop($arrFolders);

		$folders = implode('/',$arrFolders);
		
		$fullPath = STORAGE.self::getDisk()->path.'/'.$folders;

		if (empty($folders) || file_exists($fullPath))
			return;

		mkdir($fullPath, 0777, true);
	}
	public static function put($name,$data) {
		self::makeFolders($name);
		$fullPath = STORAGE.self::getDisk()->path.'/'.$name;
		if (file_put_contents($fullPath, $data))
			return $fullPath;
	}
	
	public static function get($name) {
		return file_get_contents(STORAGE.self::getDisk()->path.'/'.$name);
	}
	
	public static function exists($name) {
		return file_exists(STORAGE.self::getDisk()->path.'/'.$name);
	}
	
	public static function path($name) {
		if (self::exists($name))
			return STORAGE.self::getDisk()->path.'/';
	}
	
	public static function delete($name) {
		$names = is_array($name) ? $name : [$name];
		foreach($names as $name)
			if (!unlink(STORAGE.self::getDisk()->path.'/'.$name))
				return false;
		return true;
	}
}