<?php
use SME\Core\app;

function app($class='') {
	$helper = new class {
		public function getClass($class) {
			if (count(App::getClasses()))
				foreach(App::getClasses() as $cls)
					if ($class==$cls['name'])
						return $cls['obj'];
		}
	};
	if ($class)
		return $helper->getClass($class);
	return App::getObj();
}