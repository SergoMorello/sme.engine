<?php
namespace SME\Modules;

use SME\Modules\Storage\Object;

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
