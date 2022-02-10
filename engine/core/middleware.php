<?php
class middleware extends core {
	static $addMiddleware = [];

	private static function checkResponse($obj) {
		if (is_object($obj) && property_exists($obj, 'call') && property_exists($obj, 'props'))
			return $obj;
		else
			return app::__return($obj);
	}
	
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
						return self::checkResponse($mw['obj']($request, $nextClosure));
					else{
						app::include('app.middleware.'.$mw['name']);
						return self::checkResponse((new $mw['name'])->handle($request, $nextClosure));
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