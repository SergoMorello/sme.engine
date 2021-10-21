<?php

class app extends core {
	
	private $appService;
	
	static $app, $console, $classes = [];
	
	public function __construct($console=false) {
		
		self::$app = $this;
		
		self::$console = $console;
		
		self::include('engine.core.configure');
		
		self::include('app.appService');
		
		$this->appService = new appService;
		
		$this->defaultService('register');
		
		$this->singletonInit();
		
		new request;
			
		self::include('routes.web');
		
		self::include('routes.console');
		
		core::connectDB();
		
		core::addControllers();
		
		$this->defaultService('boot');
		
		$this->run();
		
	}
	public function __destruct() {
		
		core::disconnectDB();
		
	}
	
	public static function singleton($name, $callback) {
		self::$classes[] = [
			'name'=>$name,
			'obj'=>$callback()
		];
	}
	
	private function singletonInit() {
		foreach(app::$classes as $class)
			$this->{$class['name']} = $class['obj'];
	}
	
	public static function include($name) {
		$name = str_replace('.','/',$name);
		try {
			if (file_exists(ROOT.$name.'.php'))
				require_once(ROOT.$name.'.php');
			
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

	private function run() {
		$route = route::getRoute();
		
		if (!$route)
			abort(404);
		
		if (!$this->checkMethod($route['method']))
			abort(405);
		
		if (middleware::check($route['middleware'] ?? null))
			return;
		
		$return = function($result) {
			echo (is_array($result) || is_object($result)) ? response::json($result) : $result;
		};
		
		try {
			
			$callback = is_callable($route['callback']) ? $route['callback'] : [new $route['callback']->controller,$route['callback']->method];
			
			$return(call_user_func_array(
				$callback,
				array_values($route['props'] ?? [])
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