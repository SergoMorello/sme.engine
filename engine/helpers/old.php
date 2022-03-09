<?php

function old($name, $default = '') {
	$searchVar = function($name, $obj) use (&$searchVar) {
		if (!is_array($obj))
			return;
		foreach($obj as $key => $value) {
			if (is_array($value))
				return $searchVar($name, $value);
			else{
				if ($key == $name)
					return $value;
			}
		}
	};
	return $searchVar($name, session('__oldInputs')) ?? $default;
}
