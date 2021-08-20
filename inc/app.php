<?php
class app extends route {
	function __construct() {
		parent::__construct();
		$this->connectDB();
		
		$this->addControllers();
		$arrPage = $this->getPage();
		
		if (!$arrPage)
			view::error("Page not found",404);
		if (!$this->checkMethod($arrPage['method']))
			view::error("Method not allowed",405);
		
		if (is_callable($arrPage['callback']) && $arrPage['callback'] instanceof Closure){
			echo call_user_func_array($arrPage['callback'],$arrPage['props']);
			
		}elseif (is_array($arrPage['callback'])) {
			list($controllerName,$methodName) = $arrPage['callback'];
			if (file_exists(core::$dirC.$controllerName.".php")) {
				if (class_exists($controllerName)) {
					$controller = new $controllerName(array("url"=>$arrPage['url'],"post"=>$arrPage['post'],"get"=>$arrPage['get']));
					if (method_exists($controller,$methodName))
						echo $controller->$methodName(array("url"=>$arrPage['url'],"post"=>$arrPage['post'],"get"=>$arrPage['get']));
				}else
					view::error("Class \"".$controllerName."\" not found");
			}
		}
	}
	function __destruct() {
		$this->disconnectDB();
	}
}