<?php
abstract class core {
	static $dblink,$dirM,$dirV,$dirC,$dirCache,$dirVSys,$arrError=[];
	function __construct() {
		self::$dirM = "m/";
		self::$dirV = "v/";
		self::$dirC = "c/";
		self::$dirCache = ".cache/";
		self::$dirVSys = "inc/v/";
		
		self::newError('error',404,['message'=>'Not found']);
		self::newError('error',405,['message'=>'Method not allowed']);
	}
	function connectDB() {
		self::$dblink = new database();
		self::$dblink->connect();
	}
	function disconnectDB() {
		self::$dblink->disconnect();
	}
	public function getUrl() {
		$path = request()->data()->route;
		return $path=="" ? "/" : "/".$path;
	}
	public static function guardData($data) {
		if (is_array($data) OR $isObj=is_object($data)) {
			$ret = array();
			foreach($data as $key=>$val)
				$ret[$key] = self::guardData($val);
			return $isObj ? (object)$ret : $ret;
		}
		return htmlspecialchars(addslashes($data));
	}
	function addControllers() { 
		foreach(route::$routes as $page)
			if (is_array($page['callback']))
				if (file_exists(self::$dirC.$page['callback'][0].".php"))
					require_once(self::$dirC.$page['callback'][0].'.php');
	}
	protected function checkMethod($method) {
		return strtolower($method)==strtolower($_SERVER['REQUEST_METHOD']) ? true : false;
	}
	public static function newError($name,$code,$params) {
		self::$arrError[] = ['name'=>$name,'code'=>$code,'params'=>$params];
	}
}