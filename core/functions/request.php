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
			return isset(self::POST()[$var]) ? self::POST()[$var] : (isset($_FILES[$var]) ? self::file($var) : NULL);
	}
	public function file($var) {
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
	public function hasFile($var) {
		if (isset($_FILES[$var]))
			return true;
		return false;
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
					case "file":
						if (empty($var->tmp_name))
							return false;
					break;
				}
			}
			return true;
		};
		foreach($data as $var=>$access) {
			if (!$accessCheck($this->input($var),$access)) {
				if ($return)
					return true;
				else
					middleware::check(['validate'],$var,$access);
			}
		}
	}
}
function request() {
	return new request;
}