<?php
abstract class core {
	static $dblink, $arrConfig=[], $arrError=[], $arrCompilerView=[], $arrStorages=[];
	
	const dirM = APP.'model/';
	const dirV = APP.'view/';
	const dirC = APP.'controller/';
	const dirCache = STORAGE.'.cache/';
	const dirCompiler = STORAGE.'.cache/compiler/';
	const dirVSys = ENGINE.'view/';
	
	protected function connectDB() {
		
		if (!config::get('DB_ENABLED'))
			return;
		
		self::$dblink = new database(
			config::get('DB_TYPE'),
			config::get('DB_HOST'),
			config::get('DB_USER'),
			config::get('DB_PASS'),
			config::get('DB_NAME'),
			config::get('APP_DEBUG')
		);
		try {
			self::$dblink->connect(true);
		} catch (PDOException $e) {
			if (config::get('APP_DEBUG'))
				exceptions::throw('error',['message'=>@iconv('CP1251','UTF-8',$e->getMessage())]);
			else
				exceptions::throw('error',['message'=>'Connect DB']);
		}
	}
	
	protected function disconnectDB() {
		if (config::get('DB_ENABLED') && !is_null(core::$dblink))
			core::$dblink->disconnect();
	}
	
	protected static function request() {
		if (app::isConsole()) {
			
			$argvConsole = $_SERVER['argv'];
			if (!isset($argvConsole[1]))
				return exceptions::throw('consoleError',[
						'message'=>'Comand list:',
						'routes'=>route::list('command')
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
	
	protected static function isBase64($data) {
		if (base64_encode(base64_decode($data, true))==$data)
			return true;
		return false;
	}
	
	protected static function isJson($string) {
		if (is_string($string)) {
            @json_decode($string);
            return (json_last_error() === 0);
        }
        return false;
	}
	
	protected function addControllers() {
		foreach(route::getRoutes() as $page)
			if (!is_callable($page['callback']))
				if (file_exists(self::dirC.$page['callback']->controller.".php")) {
					try {
						
						require_once(self::dirC.$page['callback']->controller.'.php');
						
					} catch (ParseError $e) {
			
						exceptions::throw('viewError',$e);
					
					} catch (Error $e) {
						
						exceptions::throw('viewError',$e);
						
					} catch (Exception $e) {
						
						exceptions::throw('viewError',$e);
						
					} catch (ErrorException $e) {
						
						exceptions::throw('viewError',$e);
						
					}
				}
	}
	
	protected function checkMethod($method) {
		if (app::isConsole())
			return strtolower($method)=='command' ? true : false;
		else
			return strtolower($method)==strtolower($_SERVER['REQUEST_METHOD']) ? true : false;
			
	}
}