<?php
namespace SME\Core;

use SME\Exceptions\ExceptionError;
use SME\Exceptions\Http;
use SME\Core\Request\Request;

class Exception extends \Exception {
	
	private static $exceptions = [], $exceptionName = '';

	public static function __init() {
		set_exception_handler([self::class, 'throw']);
		set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext){
			throw new \SME\Exceptions\Error($errstr, 0, $errfile, $errline);
		});
	}

	public static function throw($exception) {
		if (!$exception instanceof ExceptionError) {
			try {
				if (App::include('app.Exceptions.handlerException'))
					return (new \App\Exceptions\handlerException)->render(Request::class, $exception);
			} catch (\Throwable $e) {
				throw new ExceptionError($e->getMessage(), $e->getFile(), $e->getLine());
			}
		}
		(new self)->render(Request::class, $exception);
	}
	
	public static function abort($code, $props = []) {
		die(self::throw(new Http("abort", $code)));
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
