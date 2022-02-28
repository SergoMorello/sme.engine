<?php
namespace SME\Core;

use SME\Core\Request\request;
use SME\Core\Response\Response;
use SME\Core\Model\modelCore;

class App extends Core {
	
	private $appService;
	
	private static $console,
		$classes = [],
		$objApp,
		$configure=false,
		$run = false;
	
	public function __construct($console=false) {
		
		if (self::$run)
			return;
		
		self::$objApp = new class extends Core{};
		
		self::$run = true;
		
		self::$console = $console;

		$this->checkFolders();

		self::include('engine.core.configure');
		
		self::$configure = true;
		
		self::include('app.appService');
		
		$this->appService = new \App\appService;
		
		$this->defaultService('register');
		
		$this->singletonInit();
		
		Request::__init();
		
		\Route::__init();
		
		ControllerCore::__init();
		
		$this->defaultService('boot');
		
		$this->run();
		
	}

	public function __destruct() {
		modelCore::__close();
	}
	
	public static function getObj() {
		return self::$objApp;
	}
	
	public static function getClasses() {
		return self::$classes;
	}
	
	public static function isConfigure() {
		return self::$configure;
	}
	
	private function checkFolders() {
		foreach(get_defined_constants(true)['user'] as $folder) {
			if (!file_exists($folder))
				mkdir($folder);
		}
	}

	public static function isConsole() {
		return self::$console;
	}

	public static function singleton($name, $callback) {
		self::$classes[] = [
			'name'=>$name,
			'obj'=>$callback()
		];
	}
	
	private function singletonInit() {
		foreach(self::$classes as $class)
			self::$objApp->{$class['name']} = $class['obj'];
	}
	
	public static function include($name) {
		$name = str_replace('.','/',$name);
		try {
			if (file_exists(ROOT.$name.'.php'))
				return require_once(ROOT.$name.'.php');
			
		} catch (\ParseError $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\Error $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\Exception $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\ErrorException $e) {
			
			Exception::throw('exception',$e);
			
		}
	}

	private function defaultService($method) {
		try {
			
			if (method_exists($this->appService, $method))
				$this->appService->$method();
			
		} catch (\ParseError $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\Error $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\Exception $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\ErrorException $e) {
			
			Exception::throw('exception',$e);
			
		}
	}

	public static function __return($result) {
		$result = (is_object($result) && method_exists($result, 'getContent')) ? $result->getContent() : $result;
		$result = (is_array($result) || is_object($result)) ? Response::json($result)->getContent() : $result;
		
		die((string)$result);
	}

	private function run() {
		$route = \Route::getRoute();
		
		if (!$route)
			abort(404);
		
		if (!$this->checkMethod($route['method'] ?? ''))
			abort(405);

		$routeCallback = function($route) {
			$return = (object)[
				'call' => null,
				'props' => $route['props'] ?? []
			];

			if (is_callable($route['callback'])) {
				$return->call = $route['callback'];
			}else{
				$controller = strpos($route['callback']->controller, '\\') ? $route['callback']->controller : 'App\\Controllers\\'.$route['callback']->controller;
				if (!class_exists($controller))
					throw new \Exception('Controller "'.$controller.'" not found',1);
				$return->call = [new $controller, $route['callback']->method];
			}
			
			return middleware::check($route['middleware'] ?? null, $return, new request);
		};

		try {
			$callback = $routeCallback($route);
			self::__return(call_user_func_array(
				$callback->call, 
				array_values($callback->props)
			));
			
		} catch (\ParseError $e) {
			
			Exception::throw('exception',$e);
		
		} catch (\Error $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\Exception $e) {
			
			Exception::throw('exception',$e);
			
		} catch (\ErrorException $e) {
			
			Exception::throw('exception',$e);
			
		}
	}
}