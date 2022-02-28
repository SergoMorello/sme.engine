<?php
namespace SME\Core;

class middleware extends Core {
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

	private static function checkResponse($obj) {
		if (is_object($obj) && property_exists($obj, 'call') && property_exists($obj, 'props'))
			return $obj;
		else
			return App::__return($obj);
	}
	
	public static function check($arrCheck, $controllerReturn, $request) {
		$arrCheck = is_array($arrCheck) ? $arrCheck : [$arrCheck];
		
		$nextClosure = function($request) use (&$controllerReturn){
			if (!App::isConsole())
				array_unshift($controllerReturn->props, $request);
			return (object)[
				'call' => $controllerReturn->call,
				'props' => $controllerReturn->props
			];
		};

		foreach($arrCheck as $mdw) {
			foreach(self::$addMiddleware as $mw) {
				if ($mdw == $mw['name']) {
					if (is_callable($mw['obj']) && $mw['obj'] instanceof Closure)
						self::checkResponse($mw['obj']($request, $nextClosure));
					else{
						self::checkResponse((new $mw['obj'])->handle($request, $nextClosure));
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