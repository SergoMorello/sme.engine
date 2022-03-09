<?php

class Console {
	
	public static function __callStatic($name, $args) {
		return self::callMethod($name, $args);
	}

	private static function callMethod($name, $args) {
		$obj = new \SME\Core\Route\RouteConsole;
		if (!method_exists($obj, $name))
			throw new \Exception("Method ".$name." in route not found", 1);
		return $obj->$name(...$args);
	}
}