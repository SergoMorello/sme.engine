<?php
namespace SME\Core\Model;

class Model {
	public static function __callStatic($name, $arg) {
		return self::callMethod($name, $arg);
	}

	public function __call($name, $arg) {
		return self::callMethod($name, $arg, get_object_vars($this));
	}

	private static function callMethod($name, $arg, $vars = null) {
		$obj = new ModelMethods;
		$class = get_called_class();
		if (!method_exists($obj, $name))
			throw new \Exception('Method "'.$name.'" in model "'.$class.'" not found', 1);
		$model = new $class;
		if (is_array($vars)) {
			foreach($vars as $key => $value) {
				if ($key == 'table')
					continue;
				$obj->{$key} = $value;
			}	
		}
		return $obj($model->table ?? self::className($class))->$name(...$arg);
	}

	private static function className($class) {
		return substr($class, strrpos($class, '\\') + 1);
	}
}