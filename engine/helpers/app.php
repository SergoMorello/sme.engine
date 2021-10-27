<?php
function app($class='') {
	$helper = new class {
		public function getClass($class) {
			if (count(app::getClasses()))
				foreach(app::getClasses() as $cls)
					if ($class==$cls['name'])
						return $cls['obj'];
		}
	};
	if ($class)
		return $helper->getClass($class);
	return app::getObj();
}