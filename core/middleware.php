<?php
class middleware extends core {
	static $addMiddleware=[];
	public static function check($arrCheck,...$arg) {
		if (!is_array($arrCheck))
			return;
		foreach($arrCheck as $mdw) {
			foreach(self::$addMiddleware as $mw) {
				if ($mdw==$mw['name']) {
					if (is_callable($mw['obj']) && $mw['obj'] instanceof Closure)
						return $mw['obj'](...$arg);
					else{
						require_once(MIDDLEWARE.$mw['name'].'.php');
						return (new $mw['name'])->handle(...$arg);
					}
				}
			}
		}
	}
	public static function declare($name,$obj=NULL) {
		self::$addMiddleware[] = ['name'=>$name,'obj'=>$obj];
	}
}