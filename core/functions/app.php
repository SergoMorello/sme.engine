<?php
function app($class='') {
	$helper = new class {
		function __construct() {
			foreach(app::$classes as $class)
				$this->{$class['name']} = $class['obj'];
		}
		public function _getClass($class) {
			if (count(app::$classes))
				foreach(app::$classes as $cls)
					if ($class==$cls['name'])
						return $cls['obj'];
		}
		public function call($callback, $props=[]) {
			$callback = explode("@",$callback);
			$callback = is_callable($callback[0]) ? $callback[0] : [new $callback[0],$callback[1]];
			return call_user_func_array($callback, $props);
		}
	};
	if ($class)
		return $helper->_getClass($class);
	return $helper;
}