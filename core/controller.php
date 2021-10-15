<?php
class controller extends core {
	
	private static $model;
	
	public static function model($model=null) {
		
		if (empty($model))
			return self::$model;
		
		if (file_exists(self::dirM.$model.".php")) {
			
			require_once(self::dirM.$model.'.php');
			
			if (class_exists($model))
				return self::$model = (object)[$model=>new $model];
			else
				throw new Exception('Class "'.$model.'" not found',1);
			
		}else
			throw new Exception('Model "'.$model.'" not found',1);
		
	}
}