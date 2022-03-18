<?php
namespace SME\Modules;

use SME\Modules\Storage\Object;
use SME\Core\Core;
use SME\Core\Config;

class Storage {
	public static function __callStatic($name, $arg) {
		return self::callMethod($name, $arg);
	}

	public function __call($name, $arg) {
		return self::callMethod($name, $arg);
	}

	private static function callMethod($name, $arg) {
		$obj = new Object;
		if (!method_exists($obj, $name))
			throw new \Exception('Method "'.$name.'" not fount in Storage class', 1);
		return $obj->$name(...$arg);
	}
}

class _Storage extends Core {
	
	private static $props=[];

	public static function disk($name = '') {
		if (!empty($name))
			self::$props['disk'] = $name;
		return (new Storage);
	}
	
	private static function getDisk($disk = '') {

		$disk = is_null($disk) ? config('cache.default') : $disk;
		if (!is_null($this->store))
			$store = $this->store;
		$path = config('cache.stores.'.$store)['path'] ?? null;
		if ($path)
			return $path;
		else
			throw new \Exception('Store '.$store.' not found in config cache', 1);





		return;
		$name = empty($name) ? self::$props['disk'] ?? NULL : $name;
		
		foreach(Config::get('storage') as $disk) {
			if (empty($name))
				if (isset($disk['default']) && $disk['default'])
					return (object)$disk;
			if ($disk['name']==$name)
				return (object)$disk;
		}
	}

	private static function makeFolders($path) {
		$splitPath = explode('/', $path);
		if (count($splitPath)<=1)
			return;
			
		array_pop($splitPath);

		$folders = implode('/', $splitPath);

		if (empty($folders) || is_dir($folders))
			return;

		mkdir($folders, 0777, true);
	}

	public static function put($name, $data) {
		$fullPath = self::getDisk()->path.'/'.$name;
		self::makeFolders($fullPath);
		if (file_put_contents($fullPath, $data))
			return $fullPath;
	}
	
	public static function get($name) {
		return file_get_contents(self::getDisk()->path.'/'.$name);
	}
	
	public static function exists($name) {
		return file_exists(self::getDisk()->path.'/'.$name);
	}
	
	public static function path($name) {
		if (self::exists($name))
			return self::getDisk()->path.'/'.$name;
	}
	
	public static function delete($name) {
		$names = is_array($name) ? $name : [$name];
		foreach($names as $name)
			if (!unlink(self::getDisk()->path.'/'.$name))
				return false;
		return true;
	}
}