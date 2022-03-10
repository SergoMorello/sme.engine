<?php
namespace SME\Core\Route;

use SME\Core\App;
use SME\Core\Core;

class RouteCore extends Core {
	
	private static $routes = [],
		$props = [],
		$groupProps = [],
		$current = [];
	
	protected $route, $group;

	public function __destruct() {
		$this->saveRoute();
	}

	public static function __init() {
		if (App::isConsole())
			self::__instConsole();
		else
			self::__instHttp();
	}

	protected static function __instHttp() {
		App::include('routes.web');

		\Route::group(['prefix' => 'api', 'middleware' => 'api'], function() {
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
	
	public function group(...$arg) {
		$callback = null;
		$params = $this->route;
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
				'controller' => $split[0],
				'method' => $split[1] ?? ''
			];
		})($params['callback'])	: $params['callback'];
		
		if (!App::isConfigure())
			$params['system'] = true;
		
		$this->route = $params;
		
		return $this;
	}
	
	protected function saveRoute() {
		if (!isset($this->route['url']))
			return;
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

	private static function urlMatch($url) {
		$varChars = '0-9A-Za-z\\.\\-';
		if (App::isConsole()) {
			$consoleChars = '\\-\\w\\\\\\\\';
			return preg_replace([
				'`\{['.$varChars.']{0,10}\}`is',
				'`[\s|]\{['.$varChars.']{0,10}\?\}`is'
			],
			[
				'(['.$consoleChars.']+)',
				'[\s|]*(['.$consoleChars.']+|['.$consoleChars.']*)'
			],$url);
		}else{
			return preg_replace([
				'`/`is',
				'`\{['.$varChars.']{0,10}\}`is',
				'`\\\/\{['.$varChars.']{0,10}\?\}`is'
			],
			[
				'\/',
				'(['.$varChars.']{1,})',
				'[\/|]{0,1}(['.$varChars.']{0,})'
			],$url).'[\/|\s]{0,}';
		}	
	}

	public static function getRoute() {
		$routes = self::getRoutes();
		
		if (count($routes))
			foreach($routes as $route) {
				$request = Core::request();

				//Получаем переменные в консоли
				self::parseConsoleProps($request->props, $route['props']);
				
				//Получаем переменные после знака ?
				if ($request->props && !App::isConsole())
					$route['props'] = $request->props;
				
				if ($request->get == $route['url'])
					return self::setCurrent($route);
				
				//Определяем нужный маршрут
				if (preg_match('/\s'.self::urlMatch($route['url']).'\s/is', ' '.$request->get.' ', $matchUrl)) {
					
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