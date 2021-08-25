<?php
session_name('smeSession');
session_start();

require_once('inc_config.php');
require_once('inc/inc_database.php');
require_once('inc/core.php');
require_once('inc/model.php');
require_once('inc/controller.php');
require_once('inc/viewCore.php');
require_once('inc/view.php');
require_once('inc/route.php');
require_once('inc/functions.php');
require_once('route.php');
require_once('appService.php');

class app extends route {
	function __construct() {
		header('Content-Type: text/html; charset=utf-8');
		
		$this->defaultErrors();
		
		$this->defaultService();
		
		$this->connectDB();
		
		$this->addControllers();
		
		$this->runController();
		
	}
	private function defaultErrors() {
		self::declareError('error',404,['message'=>'Not found']);
		self::declareError('error',405,['message'=>'Method not allowed']);
		self::declareError('error',500,['message'=>'Internal Server Error']);
	}
	private function defaultService() {
		$appService = new appService;
		$appService->run($this);
	}
	private function runController() {
		$route = $this->getRoute();
		
		if (!$route)
			abort(404);
		if (!$this->checkMethod($route['method']))
			abort(405);
		
		if (is_callable($route['callback']) && $route['callback'] instanceof Closure)
			echo call_user_func_array($route['callback'],array_values($route['props'] ?? []));	
		elseif (is_array($route['callback'])) {
			list($controllerName,$methodName) = $route['callback'];
			if (file_exists(core::dirC.$controllerName.".php")) {
				if (class_exists($controllerName)) {
					$controller = new $controllerName();
					if (method_exists($controller,$methodName))
						echo $controller->$methodName(...array_values($route['props'] ?? []));
					else
						view::error('error',['message'=>"Method \"".$methodName."\" not found"]);
				}else
					view::error('error',['message'=>"Class \"".$controllerName."\" not found"]);
			}
		}
	}
	function __destruct() {
		$this->disconnectDB();
	}
}