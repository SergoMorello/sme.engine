<?php
function abort($code) {
	foreach(core::$arrError as $error) {
		if ($error['code']==$code)
			return view::error($error['name'],$error['params'],$code);
	}
}