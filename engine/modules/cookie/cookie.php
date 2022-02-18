<?php

class cookie {

	public static function make($data, $time = 0) {
		if (is_array($data)) {
			foreach($data as $key => $val) {
				$arrData = is_array($val) ? $val : ["value" => $val, "date" => ($time ? time() + $time : time() + (3600*24*30))];
				setcookie($key, $arrData['value'], $arrData['date'], "/");
			}
			return true;
		}
	}

	public static function get($var) {
		return $_COOKIE[$var] ?? NULL;
	}

	public static function delete($vars) {
		$vars = is_array($vars) ? $vars : [$vars];
		foreach($vars as $var) {
			setcookie($var, NULL);
		}
		return true;
	}
}