<?php
class request {
	protected static function POST() {
		return core::guardData($_POST);
	}
	protected static function GET() {
		return core::guardData($_GET);
	}
	public function route($var) {
		if (is_string($var))
			return route::$props[$var];
	}
	public function data($var=NULL) {
		$GET = self::GET();
		if (is_string($var))
			return $GET[$var];
		elseif (is_null($var))
			return (object)$GET;
	}
	public function input($var) {
		if (is_string($var))
			return self::POST()[$var];
	}
	public function has($var) {
		if (isset(self::POST()[$var]))
			return true;
		if (isset(route::$props[$var]))
			return true;
		if (isset(self::GET()[$var]))
			return true;
		return false;
	}
	public function validate($data,$return=false) {
		if (!is_array($data))
			return;
		$accessCheck = function ($var,$access) {
			foreach(explode('|',$access) as $ac) {
				switch($ac) {
					case "string":
						if (!empty($var) && !is_string($var))
							return false;
					break;
					case "number":
						if (!empty($var) && !is_numeric($var))
							return false;
					break;
					case "required":
						if (empty($var))
							return false;
					break;
				}
			}
			return true;
		};
		foreach($data as $var=>$access) {
			if (isset(self::POST()[$var])) {
				if (!$accessCheck(self::POST()[$var],$access)) {
					if ($return)
						return true;
					else
						middleware::check(['validate'],self::POST()[$var]);
				}
			}
		}
	}
}
function request() {
	return new request;
}