<?php
namespace SME\Core;

use SME\Core\Request\Request;
use SME\Core\Response\Response;
use SME\Core\Response\ResponseObject;
use SME\Core\Route\RouteCore;
use SME\Core\Model\ModelCore;

class App extends Core {
	
	private $appService;
	
	private static $console,
		$dev,
		$classes = [],
		$objApp,
		$configure = false,
		$run = false,
		$locale = 'en',
		$include = [];
	
	public function __construct($console = false, $dev = false) {

		if (self::$run)
			return;
		
		self::$objApp = new class extends Core{};
		
		self::$run = true;
		
		self::$console = $console;

		self::$dev = $dev;

		self::singleton('path.public', function(){
			return base_path('public');
		});

		if (self::isDev() && self::checkPublicDir())
			return false;

		Exception::__init();
		
		$this->checkFolders();

		$this->autoload();

		self::include('engine.core.configure');
		
		self::$configure = true;
		
		self::include('app.appService');
		
		$this->appService = new \App\appService;
		
		$this->defaultService('register');
		
		$this->singletonInit();
		
		Request::__init();
		
		RouteCore::__init();
		
		ControllerCore::__init();
		
		$this->defaultService('boot');
		
		$this->run();
		
	}

	public function __destruct() {
		ModelCore::__close();
	}

	public function __invoke() {
		return false;
	}

	private function autoload() {
		spl_autoload_register(function($class){
			self::include(str_replace('App', 'app', $class));
		});
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
	
	public static function getLocale() {
		return self::$locale;
	}

	public static function setLocale($locale) {
		self::$locale = $locale;
	}

	private function checkFolders() {
		if (!self::isDev())
			return;
		foreach(get_defined_constants(true)['user'] as $folder) {
			if (!file_exists($folder))
				mkdir($folder);
		}
	}

	public static function isConsole() {
		return self::$console;
	}

	public static function isDev() {
		return self::$dev;
	}

	public static function findClassKey($name) {
		if (!is_string($name))
			throw new \Exception("Name bind or singleton is not string", 1);

		foreach(self::$classes as $key => $value) {
			if ($value['name'] == $name)
				return $key;
		}
		return null;
	}

	public static function bind($name, $callback) {
		$key = self::findClassKey($name);

		$class = [
			'name' => $name,
			'obj' => $callback,
			'type' => 'bind'
		];

		if (is_null($key))
			self::$classes[] = $class;
		else
			self::$classes[$key] = $class;
	}

	public static function singleton($name, $callback) {
		$key = self::findClassKey($name);

		$class = [
			'name' => $name,
			'obj' => $callback(self::getObj()),
			'type' => 'singleton'
		];

		if (is_null($key))
			self::$classes[] = $class;
		else
			self::$classes[$key] = $class;
	}
	
	private function singletonInit() {
		foreach(self::$classes as $class)
			self::$objApp->{$class['name']} = $class['obj'];
	}
	
	private static function checkPublicDir() {
		if (is_file(app('path.public').$_SERVER['REQUEST_URI']))
			return true;
	}

	public static function include($name) {
		$name = str_replace(['.','\\'],'/',$name);
		if (file_exists(ROOT.$name.'.php')) {
			return self::$include[$name] = self::$include[$name] ?? require_once(ROOT.$name.'.php');
		}
	}

	private function defaultService($method) {
		if (method_exists($this->appService, $method))
			$this->appService->$method();	
	}

	public static function __return($result) {
		$result = ((is_array($result) || is_object($result)) && !$result instanceof ResponseObject) ? Response::json($result) : $result;
		exit((string)$result);
	}

	private function run() {

		$route = \Route::getRoute();
		
		if (!$route)
			abort(404);
		
		if (!$this->checkMethod($route['method'] ?? ''))
			abort(405);

		$routeCallback = function($route) {
			$request = new \SME\Http\Request;
			$props = null;
			$closure = null;

			$middleware = Middleware::check($route['middleware'] ?? null, $request);
			if (isset($middleware->__request[0])) {
				if ($middleware->__request[0] instanceof $request)
					$props = $route['request'] ?? $middleware->__request;
				else
					$props = $middleware->__request;
			}else{
				return self::__return($middleware);
			}

			if (is_callable($route['callback'])) {
				$closure = $route['callback'];
			}else{
				$controller = strpos($route['callback']->controller, '\\') ? 
					$route['callback']->controller : 
					'App\\Controllers\\'.str_replace('/','\\', $route['callback']->controller);
				try {
					$closure = \Closure::bind(function(...$args) use (&$route) {
						return $this->{$route['callback']->method}(...$args);
					}, new $controller);
				}catch(\Error $e) {
					throw new \Exception('Controller "'.$controller.'" not found',1);
				}
			}
			
			return (object)[
				'call' => $closure,
				'props' => $props
			];
		};

		$callback = $routeCallback($route);
		
		self::__return(call_user_func_array(
			$callback->call, 
			array_values($callback->props)
		));

	}
}