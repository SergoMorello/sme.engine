<?php

class app extends core {
	
	private $appService;
	
	private static $console,
		$classes = [],
		$objApp,
		$configure=false,
		$run = false;
	
	public function __construct($console=false) {
		
		if (self::$run)
			return;
		
		self::$objApp = new class extends core{};
		
		self::$run = true;
		
		self::$console = $console;

		$this->checkFolders();

		self::include('engine.core.configure');
		
		self::$configure = true;
		
		self::include('app.appService');
		
		$this->appService = new appService;
		
		$this->defaultService('register');
		
		$this->singletonInit();
		
		new request;
		
		route::__init();
		
		controller::__init();
		
		$this->defaultService('boot');
		
		$this->run();
		
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
			
		} catch (ParseError $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Error $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Exception $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (ErrorException $e) {
			
			exceptions::throw('exception',$e);
			
		}
	}

	private function defaultService($method) {
		try {
			
			if (method_exists($this->appService, $method))
				$this->appService->$method();
			
		} catch (ParseError $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Error $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Exception $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (ErrorException $e) {
			
			exceptions::throw('exception',$e);
			
		}
	}

	public static function __return($result) {
		$result = (is_object($result) && method_exists($result, 'getContent')) ? $result->getContent() : $result;
		$result = (is_array($result) || is_object($result)) ? response::json($result)->getContent() : $result;
		
		die((string)$result);
	}

	private function run() {
		$route = route::getRoute();
		
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
				if (!class_exists($route['callback']->controller))
					throw new Exception('Controller "'.$route['callback']->controller.'" not found',1);
				$return->call = [new $route['callback']->controller, $route['callback']->method];
			}
			
			return middleware::check($route['middleware'] ?? null, $return, new request);
		};

		try {
			$callback = $routeCallback($route);
			self::__return(call_user_func_array(
				$callback->call, 
				array_values($callback->props)
			));
			
		} catch (ParseError $e) {
			
			exceptions::throw('exception',$e);
		
		} catch (Error $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (Exception $e) {
			
			exceptions::throw('exception',$e);
			
		} catch (ErrorException $e) {
			
			exceptions::throw('exception',$e);
			
		}
	}
}