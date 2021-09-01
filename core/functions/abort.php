<?php
class abort {
	public static function declare($code,$callback) {
		if (!is_numeric($code))
			return;
		core::$arrError[$code] = ['callback'=>$callback];
	}
}
function abort($code) {
	if (isset(core::$arrError[$code]) && $error = core::$arrError[$code])
		die($error['callback']($code));
	die('error');
}