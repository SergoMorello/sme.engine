<?php
namespace SME\Core\Model;

class Model {
	protected $table;
	
	public static function __callStatic($name, $arg) {
		return self::callMethod($name, $arg);
	}

	public function __call($name, $arg) {
		return self::callMethod($name, $arg);
	}

	private static function callMethod($name, $arg) {
		$obj = new ModelMethods;
		if (!method_exists($obj, $name))
			throw new \Exception("Method ".$name." in model not found", 1);
		$class = get_called_class();
		$model = new $class;
		return $obj->__init($model->table ?? $class)->$name(...$arg);
	}
}