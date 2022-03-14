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
	
	public static function check($arrCheck, $request, $runClosure, $route) {
		$arrCheck = is_array($arrCheck) ? $arrCheck : (is_null($arrCheck) ? null : [$arrCheck]);

		$nextClosure = function(...$req) use (&$request, &$runClosure, &$route) {
			$return = (object)['__next' => null];
			if (isset($req[0]) && is_object($req[0]) && $req[0] instanceof $request)
				$return->__next = $route['request'] ?? [$req];
			else
				$return->__next = $req;
			return $return;
		};

		$check = function($name) use (&$request, &$nextClosure) {
			foreach(self::$addMiddleware as $mw) {
				if ($name == $mw['name']) {
					if (is_callable($mw['obj']) && $mw['obj'] instanceof \Closure)
						return $mw['obj']($request, $nextClosure);
					else{
						return (new $mw['obj'])->handle($request, $nextClosure);
					}
				}
			}
		};
		
		if (count($arrCheck)) {
			$nextRequest = null;
			foreach($arrCheck as $mdw) {
				if (!$nextRequest = $check($mdw))
					break;
			}
			if (isset($nextRequest->__next))
				return $runClosure($nextRequest->__next);
			else
				App::__return($nextRequest);
		}else
			return $runClosure($route['request'] ?? [$request]);
	}

	public static function declare($name, $obj = NULL) {
		self::$addMiddleware[] = ['name' => $name, 'obj' => $obj];
	}
}