<?php

abstract class routeInc extends core {
	
	protected static $routes=[], $props=[], $groupProps=[], $current=[];
	
	protected $route;
	
	public function name($name) {
		$this->route['name'] = $name;
		return $this;
	}
	
	public function middleware($name) {
		$this->route['middleware'] = (is_array($name) ? $name : [$name]);
		return $this;
	}
	
	public function where($where) {
		$this->route['where'] = $where;
		return $this;
	}
	
	public static function group($params, $callback) {
		self::$groupProps = $params;
		$callback();
		self::$groupProps = [];
	}
	
	public static function list($method='') {
		$ret = [];
		foreach(self::$routes as $route)
			if ($route['method']==$method || empty($method))
				$ret[] = $route;
		return $ret;
	}
	
	public static function getRoutes() {
		return self::$routes;
	}
	
	public static function getCurrent($var=null) {
		return is_null($var) ? (object)self::$current : (is_string($var) ? self::$current[$var] ?? null : (object)self::$current);
	}
	
	public static function getProps($var=null) {
		return is_null($var) ? self::$props : (is_string($var) ? self::$props[$var] ?? null : self::$props);
	}
	
	private static function setCurrent($route) {
		self::$current = $route;
		return $route;
	}
	
	protected function setRoute($params) {
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
		
		if (!app::isConfigure())
			$params['system'] = true;
		
		$this->route = $params;
	}
	
	protected function saveRoute() {
		self::$routes[] = $this->route;
	}
	
	public static function getRoute() {
		$routes = self::$routes;
		
		$allowChars = '0-9A-Za-z';
		$urlMatch = function($url) use (&$allowChars) {
			return preg_replace([
				'`/`is',
				'`\{['.$allowChars.']{0,10}\}`isU',
				'`\\\/\{['.$allowChars.']{0,10}\?\}`isU',
				'`([\s]{0,}){['.$allowChars.']{0,10}\?\}`isU'
			],
			[
				'\/',
				'(['.$allowChars.']{1,})',
				'[\/|]{0,1}(['.$allowChars.']{0,})',
				'\\1(['.$allowChars.']{0,})|\\1'
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
				if (preg_match('/\s'.$urlMatch($route['url']).'[\/|]{0,}\s/is', ' '.$request->get.' ', $matchUrl)) {
					
					//Получаем названия переменных из маршрута
					if (preg_match_all("/\{(.*)\}/isU", $route['url'], $matchVars)) {
						
						foreach($matchVars[1] as $key=>$varName) {
							
							$varName = str_replace('?','',$varName);
							$varValue = $matchUrl[$key+1] ?? null;
							
							//Проверяем регуляркой что внутри переменных
							if (isset($route['where'][$varName]) && !empty($varValue) && !preg_match('/'.$route['where'][$varName].'/',$varValue))
								return [];
							
							$route['props'][$varName] = $varValue;
						}
					}
					
					if (isset($route['props']))
						self::$props = $route['props'];
					
					return self::setCurrent($route);
				}
			}
			
		return [];
	}
}