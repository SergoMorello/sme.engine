<?php
abstract class core {
	static $dblink,$arrConfig=[],$arrError=[],$arrCompillerView=[],$arrStorages=[];
	
	const dirM = ROOT.'model/';
	const dirV = ROOT.'view/';
	const dirC = ROOT.'controller/';
	const dirCache = STORAGE.'.cache/';
	const dirCompiller = STORAGE.'.cache/compiller/';
	const dirVSys = CORE.'view/';
	
	function connectDB() {
		$config = app()->config;
		
		if (!$config->DB_ENABLED)
			return;
		self::$dblink = new database(
			$config->DB_TYPE,
			$config->DB_HOST,
			$config->DB_USER,
			$config->DB_PASS,
			$config->DB_NAME,
			$config->APP_DEBUG
		);
		self::$dblink->connect();
	}
	function __destruct() {
		if (app()->config->DB_ENABLED && !is_null(self::$dblink))
			self::$dblink->disconnect();
	}
	public static function url() {
		$splitUrl = explode('?',$_SERVER['REQUEST_URI']);
		$splitProps = function($props) {
			$ret = [];
			$split = explode('&',$props);
			foreach($split as $sp) {
				$splitVar = explode('=',$sp);
				$ret[$splitVar[0]] = core::guardData($splitVar[1]);
			}
			return $ret;
		};
		return (object)['get'=>core::guardData($splitUrl[0]),'props'=>(isset($splitUrl[1]) ? $splitProps($splitUrl[1]) : [])];
	}
	public static function guardData($data) {
		$isObj = false;
		if (is_array($data) || $isObj=is_object($data)) {
			$ret = array();
			foreach($data as $key=>$val)
				$ret[$key] = self::guardData($val);
			return $isObj ? (object)$ret : $ret;
		}
		return htmlspecialchars(addslashes($data));
	}
	public static function isBase64($data) {
		if (base64_encode(base64_decode($data, true))==$data)
			return true;
		return false;
	}
	function addControllers() {
		foreach(route::$routes as $page)
			if (!is_callable($page['callback']))
				if (file_exists(self::dirC.$page['callback']->controller.".php"))
					require_once(self::dirC.$page['callback']->controller.'.php');
	}
	protected function checkMethod($method) {
		return strtolower($method)==strtolower($_SERVER['REQUEST_METHOD']) ? true : false;
	}
	public static function declareError($name,$code,$params) {
		if (!is_numeric($code))
			return;
		self::$arrError[$code] = ['name'=>$name,'code'=>$code,'params'=>$params];
	}
}