<?php
namespace SME\Core\Route;

use SME\Core\App;
use SME\Core\Core;

abstract class RouteCore extends Core {
	
	protected static $routes = [],
		$props = [],
		$groupProps = [],
		$groupStaticProps = [],
		$current = [];
	
	protected $route;

	protected static function __instHttp() {
		App::include('routes.web');

		self::group(['prefix' => 'api', 'middleware' => 'api'], function() {
			App::include('routes.api');
		});
		return (object)[
			'routes' => self::$routes,
			'props' => self::$props,
			'groupProps' => self::$groupProps,
			'current' => self::$current
		];
	}

	protected static function __instConsole() {
		App::include('routes.console');
	}
	
	// public function name($name) {
	// 	$this->route['name'] = $name;
	// 	return $this;
	// }
	
	// public function middleware($name) {
	// 	$this->route['middleware'] = (is_array($name) ? $name : [$name]);
	// 	return $this;
	// }
	
	// public function where($where) {
	// 	$this->route['where'] = $where;
	// 	return $this;
	// }
	
	public static function group(...$arg) {
		$callback = null;
		$params = self::$groupStaticProps;
		if (is_callable($arg[0] ?? null))
			$callback = $arg[0];
		if (is_array($arg[0] ?? null) && is_callable($arg[1] ?? null)) {
			$callback = $arg[1];
			$params = $arg[0];
		}
		
		if (is_null($callback))
			return;
		if (!is_null($params))
			self::$groupProps[] = $params;
		$callback();
		unset(self::$groupProps[array_key_last(self::$groupProps)]);
	}
	
	public static function __list($method = '') {
		self::__instHttp();
		$ret = [];
		foreach(self::$routes as $route)
			if ($route['method'] == $method || empty($method))
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
		$return = is_null($var) ? self::$props : (is_string($var) ? self::$props[$var] ?? null : self::$props);
		return empty($return) ? null : $return;
	}
	
	private static function setCurrent($route) {
		self::$current = $route;
		return $route;
	}
	
	protected function setRoute($params) {
		if (count(self::$groupProps)) {
			foreach(array_reverse(self::$groupProps) as $gp) {
				if (isset($gp['prefix']))
					$params['url'] = '/'.$gp['prefix'].(substr($params['url'],-1)=='/' ? substr_replace($params['url'],'',strlen($params['url'])-1) : $params['url']);
				if (isset($gp['middleware'])) {
					$middlewares = is_array($gp['middleware']) ? $gp['middleware'] : [$gp['middleware']];
					foreach($middlewares as $middleware) {
						$params['middleware'][] = $middleware;
					}
				}
				if (isset($gp['name']))
					$params['name'] = $gp['name'].($params['name'] ?? '');
			}
		}
		
		$params['callback'] = is_string($params['callback']) ? (function($callback) {
			$split = explode("@",$callback);
			return (object)[
				'controller'=>$split[0],
				'method'=>$split[1]
			];
		})($params['callback'])	: $params['callback'];
		
		if (!App::isConfigure())
			$params['system'] = true;
		
		$this->route = $params;
	}
	
	protected function saveRoute() {
		self::$routes[] = $this->route;
	}
	
	private static function parseConsoleProps($requestProps, &$routeProps) {
		if (count($requestProps)<=0 || !App::isConsole())
			return;
		foreach($requestProps as $prop) {
			if (($index = strrpos($prop, '--')) === false)
				continue;
				$var = explode('=', substr($prop, $index + 2));
				$name = $var[0] ?? '';
				$value = $var[1] ?? '';
				$routeProps[$name] = $value;
		}
	} 

	public static function getRoute() {
		$routes = self::getRoutes();
		
		$allowChars = '0-9A-Za-z.-';
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
		if (count($routes))
			foreach($routes as $route) {
				$request = Core::request();

				//Получаем переменные в консоли
				self::parseConsoleProps($request->props, $route['props']);
				
				//Получаем переменные после знака ?
				if ($request->props && !App::isConsole())
					$route['props'] = $request->props;
				
				if ($request->get==$route['url'])
					return self::setCurrent($route);
				
				//Определяем нужный маршрут
				if (preg_match('/\A'.$urlMatch($route['url']).'[\/|]{0,}\Z/is', $request->get, $matchUrl)) {
					
					//Получаем названия переменных из маршрута
					if (preg_match_all("/\{(.*)\}/isU", $route['url'], $matchVars)) {
						
						foreach($matchVars[1] as $key => $varName) {
							
							$varName = str_replace('?','',$varName);
							$varValue = $matchUrl[$key+1] ?? null;
							
							//Проверяем регуляркой что внутри переменных
							if (isset($route['where'])) {
								$where = (isset($route['where'][0]) && is_array($route['where'][0])) ? $route['where'][0] : $route['where'];
								if (isset($where[$varName]) && !empty($varValue) && !preg_match('/^'.$where[$varName].'$/isU', $varValue))
									return [];
							}

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