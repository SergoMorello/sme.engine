<?php
class controller extends core {
	
	private static $model;
	
	public static function model($model=null) {
		
		if (empty($model))
			return (object)self::$model;
		
		if (file_exists(self::dirM.$model.".php")) {
			
			require_once(self::dirM.$model.'.php');
			
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