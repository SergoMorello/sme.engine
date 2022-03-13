<?php
namespace SME\Core;

class Middleware extends Core {
	static $addMiddleware = [];

	public static function init() {
		$initFnc = function($init) {
			foreach($init as $name => $path) {
				$nameClass = $path;
				if (($index = strrpos($path, '/')) || ($index = strrpos($path, '.'))) 
					$nameClass = substr($path, $index + 1);
				
				App::include($path);
				self::declare($name, $nameClass);
			}
		};
		if ($init = App::include('app.appMiddleware')) {
			if (App::isConsole()) {
				if (isset($init['console']))
					$initFnc($init['console']);
			}else{
				if (isset($init['http']))
					$initFnc($init['http']);
			}	
		}
	}
	
	public static function check($arrCheck, $request) {
		$arrCheck = is_array($arrCheck) ? $arrCheck : [$arrCheck];
		
		$nextClosure = function(...$request) {
			return (object)[
				'__request' => $request
			];
		};

		foreach($arrCheck as $mdw) {
			foreach(self::$addMiddleware as $mw) {
				if ($mdw == $mw['name']) {
					if (is_callable($mw['obj']) && $mw['obj'] instanceof \Closure)
						return $mw['obj']($request, $nextClosure);
					else{
						return (new $mw['obj'])->handle($request, $nextClosure);
					}
				}
			}
		}
		return $nextClosure($request);
	}

	public static function declare($name, $obj = NULL) {
		self::$addMiddleware[] = ['name' => $name, 'obj' => $obj];
	}
}