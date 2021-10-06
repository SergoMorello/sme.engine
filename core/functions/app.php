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
	};
	if ($class)
		return $helper->_getClass($class);
	return $helper;
}