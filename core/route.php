<?php
class route extends core {
	static $routes=[],$props=[],$groupProps=[];
	function __construct($index=0) {
		$this->lastIndex = $index;
	}
	protected function getRoute() {
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
				$url = $this->url();
				if ($url->get==$route['url'])
					return $route;
				//Определяем нужный маршрут
				if ($route['url']!="/" && preg_match('/\s'.$urlMath($route['url']).'[\/|]{0,}\s/is', ' '.$url->get.' ', $matchUrl)) {
					//Получаем названия переменных из маршрута
					if (preg_match_all("/\{(.*)\}/isU", $route['url'], $matchVars)) {
						foreach($matchVars[1] as $key=>$varName)
							$route['props'][$varName] = $matchUrl[$key+1];
						
					}
					//Получаем переменные после знака ?
					if ($url->props)
							foreach($url->props as $propName=>$propVal)
								$route['props'][$propName] = $propVal;
					
					self::$props = $route['props'];
					
					return $route;
				}
			}
		return array();
	}
	private static function setRoute($params) {
		if (count(self::$groupProps)) {
			$gp = self::$groupProps;
			if (isset($gp['prefix']))
				$params['url'] = '/'.$gp['prefix'].$params['url'];
		}
		$params['callback'] = is_string($params['callback']) ? explode("@",$params['callback']) : $params['callback'];
		$index = array_push(self::$routes,$params)-1;
		return new self($index);
	}
	public static function get($url,$callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"get"));
	}
	public static function post($url,$callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"post"));
	}
	public static function put($url,$callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"put"));
	}
	public static function delete($url,$callback) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"delete"));
	}
	public function name($name) {
		self::$routes[$this->lastIndex]['name'] = $name;
		return $this;
	}
	public function middleware($name) {
		self::$routes[$this->lastIndex]['middleware'] = (is_array($name) ? $name : [$name]);
		return $this;
	}
	public static function group($params,$callback) {
		self::$groupProps = $params;
		$callback();
		self::$groupProps = [];
	}
}