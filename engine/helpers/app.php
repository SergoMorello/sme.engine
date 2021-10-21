<?php
function app($class='') {
	$helper = new class {
		public function getClass($class) {
			if (count(app::$classes))
				foreach(app::$classes as $cls)
					if ($class==$cls['name'])
						return $cls['obj'];
		}
	};
	if ($class)
		return $helper->getClass($class);
	return app::$app;
}