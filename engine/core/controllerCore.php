<?php
namespace SME\Core;

class ControllerCore extends Core {
	
	private static $model;
	
	private static function getPath($file, $default) {
		return preg_match('/[.\/]+/', $file) ? $file : $default.'.'.$file;
	}

	public static function __init() {
		App::include('app.Controllers.Controller');
		foreach(\Route::getRoutes() as $page)
			if (!is_callable($page['callback'])) {
				App::include(self::getPath($page['callback']->controller, 'app.controllers'));
			}
	}

	public static function model($model = null) {
		
		if (empty($model))
			return (object)self::$model;
		
		$model = '\\App\\Models\\'.$model;
		try {
			return self::$model[$model] = new $model;
		}catch(\Error $e) {
			throw new \Exception('Class "'.$model.'" not found',1);
		}
	}
}