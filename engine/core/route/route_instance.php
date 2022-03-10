<?php
namespace SME\Core\Route;

class RouteInstance {
	public static function __callStatic($name, $args) {
		return self::callMethod($name, $args);
	}

	private static function callMethod($name, $args) {
		$obj = null;
		switch(get_called_class()) {
			case 'Console':
				$obj = new RouteConsole;
			break;
			case 'Route':
				$obj = new RouteWeb;
			break;
		}
		if (!method_exists($obj, $name) || is_null($obj))
			throw new \Exception("Method ".$name." in route not found", 1);
		return $obj->$name(...$args);
	}
}