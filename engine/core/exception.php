<?php
namespace SME\Core;

class HttpException extends \Exception {}

class Exception extends Core {
	private static $exceptions = [], $exceptionName = '';

	public static function throw($exceptionName, $arg) {
		self::$exceptionName = $exceptionName;

		if (App::include('app.Exceptions.handlerException')) {

			$handler = new \App\Exceptions\handlerException;

			App::__return($handler->render(request(), $arg));
		}
	}
	
	public static function abort($code, $props = []) {
		try{
			throw new HttpException("abort", $code);
		} catch (HttpException $e) {
			self::throw($code, $e);
		}
	}
	
	public static function declare($name,$obj=NULL) {
		foreach(self::$exceptions as $key=>$exception)
			if ($exception['name']==$name)
				return self::$exceptions[$key] = ['name'=>$name,'obj'=>$obj];
		self::$exceptions[] = ['name'=>$name,'obj'=>$obj];
	}

	protected function render($request, $exception) {
		foreach(self::$exceptions as $ex) {
			if (self::$exceptionName == $ex['name']) {
				if (is_callable($ex['obj']) && $ex['obj'] instanceof \Closure) {
					return App::__return($ex['obj']($exception));
				}
			}
		}

		App::__return($exception);
	}
}
