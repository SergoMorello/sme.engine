<?php
namespace SME\Core;

class ControllerCore extends Core {
	
	private static $model;
	
	private static function getPath($file, $default) {
		return preg_match('/[.\/]+/', $file) ? $file : $default.'.'.$file;
	}

	public static function __init() {
		App::include('app.controllers.controller');
		foreach(\Route::getRoutes() as $page)
			if (!is_callable($page['callback'])) {
				App::include(self::getPath($page['callback']->controller, 'app.controllers'));
			}
	}

	public static function model($model = null) {
		
		if (empty($model))
			return (object)self::$model;
		
		if (App::include(self::getPath($model, 'app.models'))) {
			$model = '\\App\\Models\\'.$model;
			if (class_exists($model))
				return self::$model[$model] = new $model;
			else
				throw new \Exception('Class "'.$model.'" not found',1);
			
		}else
			throw new \Exception('Model "'.$model.'" not found',1);
		
	}
}