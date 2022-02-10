<?php
class middleware extends core {
	static $addMiddleware = [];


	public static function check($arrCheck, $controllerReturn, $request) {
		$arrCheck = is_array($arrCheck) ? $arrCheck : [$arrCheck];
		
		$nextClosure = function($request) use (&$controllerReturn){
			if (!app::isConsole())
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
						return $mw['obj']($request, $nextClosure);
					else{
						app::include('app.middleware.'.$mw['name']);
						return (new $mw['name'])->handle($request, $nextClosure);
					}
				}
			}
		}
		
		return $nextClosure($request);
	}
	
	public static function declare($name, $obj = NULL) {
		self::$addMiddleware[] = ['name'=>$name,'obj'=>$obj];
	}
}