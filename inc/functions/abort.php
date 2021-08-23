<?php
function abort($code) {
	foreach(core::$arrError as $error) {
		if ($error['code']==$code) {
			header($_SERVER['SERVER_PROTOCOL']." ".$code);
			ob_clean();
			$view = new view;
			$error['params']['code'] = $code;
			echo $view->addView($error['name'],$error['params'],true);
			die();
		}
	}
}