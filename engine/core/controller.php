<?php

class controller extends core {
	
	private static $model;
	
	private static function getPath($file, $default) {
		return preg_match('/[.\/]+/', $file) ? $file : $default.'.'.$file;
	}

	public static function __init() {
		foreach(route::getRoutes() as $page)
			if (!is_callable($page['callback'])) {
				app::include(self::getPath($page['callback']->controller, 'app.controller'));
			}
	}

	public static function model($model = null) {
		
		if (empty($model))
			return (object)self::$model;
		
		if (app::include(self::getPath($model, 'app.model'))) {
			//app::include(self::getPath($model, 'app.model'));
			
			if (class_exists($model)) {
				$newModel = new $model;
				$newModel->__init();
				return self::$model[$model] = $newModel;
			}else
				throw new Exception('Class "'.$model.'" not found',1);
			
		}else
			throw new Exception('Model "'.$model.'" not found',1);
		
	}
}