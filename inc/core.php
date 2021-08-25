<?php
abstract class core {
	static $dblink,$arrError=[],$arrCompillerView=[];
	
	const dirM = 'm/';
	const dirV = 'v/';
	const dirC = 'c/';
	const dirCache = '.cache/';
	const dirVSys = 'inc/v/';
	
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
				if (file_exists(self::dirC.$page['callback'][0].".php"))
					require_once(self::dirC.$page['callback'][0].'.php');
	}
	protected function checkMethod($method) {
		return strtolower($method)==strtolower($_SERVER['REQUEST_METHOD']) ? true : false;
	}
	public static function declareError($name,$code,$params) {
		if (!is_numeric($code))
			return;
		self::$arrError[$code] = ['name'=>$name,'code'=>$code,'params'=>$params];
	}
	public static function declareCompiller($name,$return) {
		self::$arrCompillerView[] = ['name'=>$name,'return'=>$return];
	}
}