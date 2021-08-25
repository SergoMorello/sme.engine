<?php
class controller extends core {
	protected $model;
	function model($model) {
		if (file_exists(self::dirM.$model.".php")) {
			require_once(self::dirM.$model.'.php');
			if (class_exists($model))
				$this->model = (object)[$model=>new $model];
			else
				view::error('error',['message'=>'Class \"'.$model.'\" not found']);
		}else
			view::error('error',['message'=>'Model \"'.$model.'\" not found']);
	}
}