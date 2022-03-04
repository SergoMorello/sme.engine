<?php
namespace SME\Core;

use SME\Core\Exceptions\Http;

class Exception extends \Exception {
	
	private static $exceptions = [], $exceptionName = '';

	public static function throw($arg) {
		$handler = new \App\Exceptions\handlerException;

		App::__return($handler->render(request(), $arg));
	}
	
	public static function abort($code, $props = []) {
		try{
			throw new Http("abort", $code);
		} catch (Http $e) {
			self::throw($e);
		}
	}
	
	public static function make($class, $closure) {
		if (is_null($class))
			self::$exceptions['default'] = [
				'class' => null,
				'closure' => $closure
			];
		else
			self::$exceptions[] = [
				'class' => $class,
				'closure' => $closure
			];
	}

	public static function declare($name, $obj = NULL) {
		foreach(self::$exceptions as $key => $exception)
			if (($exception['name'] ?? false) == $name)
				return self::$exceptions[$key] = ['name' => $name, 'obj' => $obj];
		self::$exceptions[] = ['name' => $name, 'obj' => $obj];
	}

	protected function render($request, $exception) {
		foreach(self::$exceptions as $ex) {
			if (isset($ex['class']) && $exception instanceof $ex['class']) {
				return App::__return($ex['closure']($exception));
				continue;
			}
			// if (self::$exceptionName == ($ex['name'] ?? false)) {
			// 	if (is_callable($ex['obj']) && $ex['obj'] instanceof \Closure) {
			// 		return App::__return($ex['obj']($exception));
			// 	}
			// }
		}
		if (isset(self::$exceptions['default']))
			App::__return(self::$exceptions['default']['closure']($exception));
		App::__return($exception);
	}
}
