<?php
class app extends route {
	function __construct() {
		parent::__construct();
		$this->connectDB();
		
		$this->addControllers();
		$route = $this->getRoute();
		
		if (!$route)
			abort(404);
		if (!$this->checkMethod($route['method']))
			abort(405);
		
		if (is_callable($route['callback']) && $route['callback'] instanceof Closure)
			echo call_user_func_array($route['callback'],array_values($route['props'] ?? []));	
		elseif (is_array($route['callback'])) {
			list($controllerName,$methodName) = $route['callback'];
			if (file_exists(core::$dirC.$controllerName.".php")) {
				if (class_exists($controllerName)) {
					$controller = new $controllerName();
					if (method_exists($controller,$methodName))
						echo $controller->$methodName(...array_values($route['props'] ?? []));
				}else
					view::error("Class \"".$controllerName."\" not found");
			}
		}
	}
	function __destruct() {
		$this->disconnectDB();
	}
}