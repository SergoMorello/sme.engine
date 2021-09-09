<?php

function old($key,$value='') {
	$inputs = session('__oldInputs');
	return isset($inputs[$key]) ? $inputs[$key] : $value;
}
