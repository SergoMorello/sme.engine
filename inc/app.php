<?php
class app extends route {
	private $controller;
	function __construct() {
		$this->connectDB();
		$this->view = new view;
		$this->addControllers();
		$arrPage = $this->getPage();
		
		if (!$arrPage)
			view::error("Page not found",404);
		$this->controller = (object)array();
		
		ob_start("app::obReplace");
		if (is_callable($arrPage['callback']) && $arrPage['callback'] instanceof Closure){
			echo call_user_func_array($arrPage['callback'],$arrPage['params']);
		}elseif (is_array($arrPage['callback'])) {
			list($controllerName,$methodName) = $arrPage['callback'];	
			if (file_exists(core::$dirC.$controllerName.".php")) {
				if (class_exists($controllerName)) {
					$this->controller = new $controllerName(array("url"=>$arrPage['url'],"post"=>$arrPage['post'],"get"=>$arrPage['get']));
					$this->view->setController($this->controller);
					
					if (method_exists($this->controller,$methodName))
						echo $this->controller->$methodName(array("url"=>$arrPage['url'],"post"=>$arrPage['post'],"get"=>$arrPage['get']));
				}else
					view::error("Class \"".$controllerName."\" not found");
			}
		}
		ob_end_flush();	
	}
	function __destruct() {
		$this->disconnectDB();
	}
	function obReplace($buffer) {
		$buffer = preg_replace_callback("/\{{(.*)\}}/", function($var){
			$nameObj = $var[1];
				if (preg_match("/(.*)\((.*)\)/",$nameObj,$var2)) {
					$nameObjFnc = $var2[1];
					if (method_exists($this->controller,$nameObjFnc))
						return $this->controller->$nameObjFnc($var2[2]);
				}
			if (property_exists($this->controller,$nameObj))
				return $this->controller->$nameObj;
			return $var[0];
		}, $buffer);
		return $buffer;
	}
}