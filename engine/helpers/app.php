<?php
use SME\Core\App;

function app($class = '') {
	$helper = new class {
		public function getClass($class) {
			if (count(App::getClasses()))
				foreach(App::getClasses() as $cls)
					if ($class == $cls['name']) {
						if ($cls['type'] == 'bind')
							return $cls['obj'](App::getObj());
						else
							return $cls['obj'];
					}
						
		}
	};
	if ($class)
		return $helper->getClass($class);
	return App::getObj();
}