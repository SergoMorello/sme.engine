<?php
class request extends core {
	
	private static $_server, $_get, $_post;
	
	public function __construct() {
		self::$_server = $_SERVER;
		self::$_get = core::guardData($_GET);
		self::$_post = core::guardData($_POST);
	}
	
	public static function route($var) {
		if (is_string($var))
			return route::$props[$var];
	}
	
	public static function server($var='') {
		if (!is_string($var))
			return null;
		if (empty($var))
			return (object)self::$_server;
		return self::$_server[$var] ?? null;
	}
	
	public static function input($var) {
		if (is_string($var)) {
			$splitVars = explode('.',$var);
			if (isset($_POST[$splitVars[0]]) && core::isJson($_POST[$splitVars[0]])) {
				$json = json_decode($_POST[$splitVars[0]]);
				unset($splitVars[0]);
				
				foreach($splitVars as $var) {
					if (is_numeric($var) && isset($json[$var]))
						$json = $json[$var];
					if (is_string($var) && isset($json->$var))
						$json = $json->$var;
				}
				
				return $json;
			}
			return self::$_post[$var] ?? self::$_get[$var] ?? (isset($_FILES[$var]) ? self::file($var) : NULL);
		}
	}
	
	public static function file($var) {
		if (!is_string($var))
			return;
		return (new class($_FILES[$var]) {
			function __construct($file) {
				foreach($file as $key=>$value)
					$this->$key = $value;
			}
			public function store($path="",$disk=""){
				storage::disk($disk)->put($path.'/'.$this->name,$this->getData());
			}
			public function storeAs($path,$name,$disk=""){
				storage::disk($disk)->put($path.'/'.$name,$this->getData());
			}
			public function getData() {
				return file_get_contents($this->tmp_name);
			}
			public function getPath() {
				return $this->tmp_name;
			}
		});
		return (object)$file;
	}
	
	public static function hasFile($var) {
		if (isset($_FILES[$var]))
			return true;
		return false;
	}
	
	public static function has($var) {
		if (isset(self::$_post[$var]))
			return true;
		if (isset(route::$props[$var]))
			return true;
		if (isset(self::$_get[$var]))
			return true;
		return false;
	}
	
	public static function json() {
		return json_decode(file_get_contents('php://input'));
	}
	
	public static function validate($data,$return=false) {
		if (!is_array($data))
			return;
		
		$accessCheck = function ($var,$access) {
			foreach(explode('|',$access) as $ac) {
				switch($ac) {
					case "string":
						if (!empty($var) && !is_string($var))
							return $ac;
					break;
					case "number":
						if (!empty($var) && !is_numeric($var))
							return $ac;
					break;
					case "required":
						if (empty($var))
							return $ac;
					break;
					case "file":
						if (isset($var->tmp_name) && empty($var->tmp_name))
							return $ac;
					break;
					case "base64":
						if (!core::isBase64($var))
							return $ac;
					break;
					case "json":
						if (!core::isJson($var))
							return $ac;
					break;
				}
			}
			return false;
		};
		$arrErr = [];
		foreach($data as $var=>$access)
			if ($accessErr = $accessCheck(
									isset(self::$_post[$var]) ? stripslashes(htmlspecialchars_decode(self::$_post[$var])) : (isset($_FILES[$var]) ? self::file($var) : NULL)
								,$access))
				$arrErr[] = [
					'name'=>$var,
					'access'=>$accessErr
				];
		
		if (count($arrErr)) {
			if ($return)
				return true;
			else
				exceptions::throw('validate',$arrErr);
		}
	}
}
function request() {
	return new request;
}