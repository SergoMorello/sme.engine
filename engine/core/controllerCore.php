<?php
namespace SME\Core;

use SME\Core\Route\RouteCore;

class ControllerCore extends Core {
	
	private static $model;
	
	private static function getPath($file, $default) {
		return preg_match('/[.\/]+/', $file) ? $file : $default.'.'.$file;
	}

	public static function __init() {
		App::include('app.Controllers.Controller');
		foreach(RouteCore::getRoutes() as $page)
			if (!$page)
				continue;
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