<?php
class route extends core {
	static $routes=[], $props=[], $groupProps=[], $current=[];
	protected function __construct($index=0) {
		$this->lastIndex = $index;
	}
	
	public static function getRoute() {
		$routes = self::$routes;
		
		$allowChars = '0-9A-Za-z';
		$urlMath = function($url) use (&$allowChars) {
			return preg_replace([
				'`/`is',
				'`\{[0-9A-Za-z]{0,10}\}`isU',
				'`\\\/\{[0-9A-Za-z]{0,10}\?\}`isU'
			],
			[
				'\/',
				'(['.$allowChars.']{0,})',
				'[\/|]{0,1}(['.$allowChars.']{0,})'
			],$url);
		};
		if ($routes)
			foreach($routes as $route) {
				$request = core::request();
				
				//Получаем переменные после знака ?
				if ($request->props)
					$route['props'] = $request->props;
				
				if ($request->get==$route['url'])
					return self::setCurrent($route);
				
				//Определяем нужный маршрут
				if ($route['url']!="/" && preg_match('/\s'.$urlMath($route['url']).'[\/|]{0,}\s/is', ' '.$request->get.' ', $matchUrl)) {
					//Получаем названия переменных из маршрута
					if (preg_match_all("/\{(.*)\}/isU", $route['url'], $matchVars)) {
						foreach($matchVars[1] as $key=>$varName)
							$route['props'][$varName] = $matchUrl[$key+1];
						
					}
					
					if (isset($route['props']))
						self::$props = $route['props'];
					
					return self::setCurrent($route);
				}
			}
			
		return array();
	}
	
	public static function list($method='') {
		$ret = [];
		foreach(self::$routes as $route)
			if ($route['method']==$method || empty($method))
				$ret[] = $route;
		return $ret;
	}
	
	private static function setCurrent($route) {
		self::$current = $route;
		return $route;
	}
	
	private static function setRoute($params) {
		if (count(self::$groupProps)) {
			$gp = self::$groupProps;
			if (isset($gp['prefix']))
				$params['url'] = '/'.$gp['prefix'].(substr($params['url'],-1)=='/' ? substr_replace($params['url'],'',strlen($params['url'])-1) : $params['url']);
			if (isset($gp['middleware']))
				$params['middleware'] = $gp['middleware'];
		}
		$params['callback'] = is_string($params['callback']) ? (function($callback) {
			$split = explode("@",$callback);
			return (object)[
				'controller'=>$split[0],
				'method'=>$split[1]
			];
		})($params['callback'])	: $params['callback'];
		$index = array_push(self::$routes,$params)-1;
		return new self($index);
	}
	
	public static function get($url, $callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"get"));
	}
	
	public static function post($url, $callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"post"));
	}
	
	public static function put($url, $callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"put"));
	}
	
	public static function delete($url, $callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"delete"));
	}
	
	public static function console($url, $callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"console"));
	}
	
	public function name($name) {
		self::$routes[$this->lastIndex]['name'] = $name;
		return $this;
	}
	
	public function middleware($name) {
		self::$routes[$this->lastIndex]['middleware'] = (is_array($name) ? $name : [$name]);
		return $this;
	}
	
	public static function group($params, $callback) {
		self::$groupProps = $params;
		$callback();
		self::$groupProps = [];
	}
}