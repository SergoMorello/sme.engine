<?php
abstract class core {
	static $dblink,
		$arrError=[],
		$arrCompilerView=[];
	
	const dirM = APP.'model/';
	const dirV = APP.'view/';
	const dirC = APP.'controller/';
	const dirCache = STORAGE.'.cache/';
	
	protected function connectDB() {
		
		if (!config::get('app.dbEnabled'))
			return;
		
		self::$dblink = new database(
			config::get('app.dbType'),
			config::get('app.dbHost'),
			config::get('app.dbUser'),
			config::get('app.dbPassword'),
			config::get('app.dbName'),
			config::get('app.debug')
		);
		
		try {
			self::$dblink->connect(true);
		} catch (PDOException $e) {
			if (config::get('app.debug'))
				exceptions::throw('error',['message'=>$e->getMessage()]);
			else
				exceptions::throw('error',['message'=>'Connect DB']);
		}
	}
	
	protected function disconnectDB() {
		if (config::get('app.dbEnabled') && !is_null(core::$dblink))
			core::$dblink->disconnect();
	}
	
	protected static function request() {
		if (app::isConsole()) {
			
			$argvConsole = $_SERVER['argv'];
			if (!isset($argvConsole[1]))
				return exceptions::throw('consoleError',[
						'message'=>'Comand list:',
						'routes'=>route::__list('command')
					]);
			unset($argvConsole[0]);
			$get = implode(' ', $argvConsole);
			unset($argvConsole[1]);
			return (object)['get'=>core::guardData($get),'props'=>$argvConsole];
			
		}else{
			
			$splitUrl = explode('?',$_SERVER['REQUEST_URI']);
			$splitProps = function($props) {
				$ret = [];
				$split = explode('&',$props);
				foreach($split as $sp) {
					$splitVar = explode('=',$sp);
					$ret[$splitVar[0]] = core::guardData($splitVar[1] ?? null);
				}
				return $ret;
			};
			return (object)['get'=>core::guardData($splitUrl[0]).'/','props'=>(isset($splitUrl[1]) ? $splitProps($splitUrl[1]) : [])];
		}
	}
	
	public static function call($callback, $props=[]) {
		$callback = explode("@",$callback);
		$callback = is_callable($callback[0]) ? $callback[0] : [new $callback[0],$callback[1]];
		return call_user_func_array($callback, $props);
	}
	
	protected static function guardData($data) {
		$isObj = false;
		if (is_array($data) || $isObj=is_object($data)) {
			$ret = [];
			foreach($data as $key=>$val)
				$ret[$key] = self::guardData($val);
			return $isObj ? (object)$ret : $ret;
		}
		return htmlspecialchars(addslashes($data));
	}
	
	protected function checkMethod($method) {
		if (app::isConsole())
			return strtolower($method)=='command' ? true : false;
		else
			return strtolower($method)==strtolower($_SERVER['REQUEST_METHOD']) ? true : false;
			
	}
}