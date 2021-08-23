<?php
class route extends core {
	static $routes=[],$props=[];
	function __construct($index=0) {
		parent::__construct();
		$this->lastIndex = $index;
	}
	protected function getRoute() {
		$routes = self::$routes;
		$urlMath = function($url) {
			return preg_replace([
				'`/`is',
				'`{(.*)}`isU'
			],
			[
				'\/',
				'(.*)'
			],$url);
		};
		if ($routes)
			foreach($routes as $route) {
				if ($this->getUrl()==$route['url'])
					return $route;
				//Определяем нужный маршрут
				if ($route['url']!="/" && preg_match('/\s'.$urlMath($route['url']).'\s/is', ' '.$this->getUrl().' ', $matchUrl)) {
					//Получаем названия переменных из маршрута
					if (preg_match_all("/\{(.*)\}/isU", $route['url'], $matchVars)) {
						foreach($matchVars[1] as $key=>$varName)
							$route['props'][$varName] = $matchUrl[$key+1];
						self::$props = $route['props'];
					}
					return $route;
				}
			}
		return array();
	}
	private static function setRoute($params) {
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
	}
}