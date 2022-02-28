<?php
namespace SME\Core;

class HttpException extends \Exception {}

class exceptions extends core {
	private static $exceptions = [], $exceptionName = '';

	public static function throw($exceptionName, $arg) {
		self::$exceptionName = $exceptionName;

		if (app::include('app.exceptions.handlerException')) {

			$handler = new handlerException;

			app::__return($handler->render(request(), $arg));
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
				if (is_callable($ex['obj']) && $ex['obj'] instanceof Closure) {
					return app::__return($ex['obj']($exception));
				}
			}
		}

		app::__return($exception);
	}
}
