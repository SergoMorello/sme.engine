<?php
function abort($code) {
	if (isset(core::$arrError[$code]) && $error = core::$arrError[$code])
			return view::error($error['name'],$error['params'],$code);
	die('error');
}