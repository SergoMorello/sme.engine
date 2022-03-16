<?php
namespace SME\Core;

use SME\Core\Route\RouteCore;

class ControllerCore extends Core {
	
	private static $model;
	

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