<?php

class exceptions extends core {
	private static $exceptions=[];
	
	public static function throw($arrCheck,...$arg) {
		$arrCheck = is_array($arrCheck) ? $arrCheck : [$arrCheck];
		foreach($arrCheck as $exc) {
			foreach(self::$exceptions as $ex) {
				if ($exc==$ex['name']) {
					if (is_callable($ex['obj']) && $ex['obj'] instanceof Closure)
						die($ex['obj'](...$arg));
					else{
						require_once(EXCEPTIONS.$ex['name'].'.php');
						die((new $ex['name'])->handle(...$arg));
					}
				}
			}
		}
	}
	
	public static function abort($code, $props=[]) {
		self::throw($code, $props);
	}
	
	public static function declare($name,$obj=NULL) {
		foreach(self::$exceptions as $key=>$exception)
			if ($exception['name']==$name)
				return self::$exceptions[$key] = ['name'=>$name,'obj'=>$obj];
		self::$exceptions[] = ['name'=>$name,'obj'=>$obj];
	}
}
