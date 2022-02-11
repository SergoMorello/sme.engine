<?php
class request extends core {
	
	private static $_server, $_get, $_post, $_headers;
	
	public function __construct() {
		self::$_server = $_SERVER;
		self::$_get = core::guardData($_GET);
		self::$_post = core::guardData($_POST);
		self::$_headers = $this->getallheaders();
	}

	public static function route($var) {
		if (is_string($var))
			return route::getProps($var);
	}
	
	public static function server($var='') {
		if (!is_string($var))
			return null;
		if (empty($var))
			return (object)self::$_server;
		return self::$_server[$var] ?? null;
	}
	
	public static function all() {
		return count(self::$_post)>0 ? self::$_post : (count(self::$_get)>0 ? self::$_get : (count($_FILES)>0 ? self::file : NULL));
	}

	private function getallheaders() {
		$headers = [];
		foreach (self::$_server as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}

	public static function header($name = null) {
		if (is_null($name)) {
			return (object)self::$_headers;
		}else{
			if (isset(self::$_headers[$name]))
				return self::$_headers[$name];
		}
	}

	public static function input($var) {
		if (is_string($var)) {
			$splitVars = explode('.',$var);
			if (isset($_POST[$splitVars[0]]) && is()->json($_POST[$splitVars[0]])) {
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
			public function __construct($file) {

				foreach($file['name'] as $key=>$value) {
					if (empty($value))
						continue;
					$this->$key = new class([
						'name'=>$value,
						'type'=>$file['type'][$key],
						'tmp_name'=>$file['tmp_name'][$key],
						'error'=>$file['error'][$key],
						'size'=>$file['size'][$key]
					]) {
						public function __construct($props) {
							foreach($props as $key=>$value)
								$this->$key = $value;
						}
						public function getData() {
							return file_get_contents($this->tmp_name);
						}
						public function getName() {
							return $this->name;
						}
						public function getType() {
							return $this->type;
						}
						public function getPath() {
							return $this->tmp_name;
						}
						public function getError() {
							return $this->error;
						}
						public function getSize() {
							return $this->size;
						}
						public function store($path="",$disk="") {
							return storage::disk($disk)->put($path.'/'.$this->name,$this->getData());
						}
						public function storeAs($path,$name,$disk="") {
							return storage::disk($disk)->put($path.'/'.$name,$this->getData());
						}
					};
				}
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
		if (route::getProps($var))
			return true;
		if (isset(self::$_get[$var]))
			return true;
		return false;
	}
	
	public static function json() {
		return json_decode(file_get_contents('php://input'));
	}
	
	public static function validate($data, $return = false) {
		if (!is_array($data))
			return;

		$arrErr = [];
		foreach($data as $var=>$access)
			if ($accessErr = validate::checkVar(
								self::input($var) ?? self::route($var)
									//isset(self::$_post[$var]) ? stripslashes(htmlspecialchars_decode(self::$_post[$var])) : (isset($_FILES[$var]) ? self::file($var) : NULL)
								,$access))
				$arrErr[] = [
					'name' => $var,
					'access' => $accessErr
				];
		
		if (count($arrErr)) {
			if ($return)
				return true;
			else
				exceptions::throw('validate',$arrErr);
		}
	}
}