<?php

use SME\Core\Route\RouteCore;
use SME\Core\Route\RouteObject;
use SME\Core\App;

class Route extends RouteCore {

	public static function __init() {
		if (App::isConsole())
			self::__instConsole();
		else
			self::__instHttp();
	}
	
	public static function get($url, $callback) {
		return new RouteObject([
			"url" => $url,
			"callback" => $callback,
			"method" => "get"
			]);
	}
	
	public static function post($url, $callback) {
		return new RouteObject([
			"url" => $url,
			"callback" => $callback,
			"method" => "post"
			]);
	}
	
	public static function put($url, $callback) {
		return new RouteObject([
			"url" => $url,
			"callback" => $callback,
			"method" => "put"
			]);
	}
	
	public static function delete($url, $callback) {
		return new RouteObject([
			"url" => $url,
			"callback" => $callback,
			"method" => "delete"
			]);
	}

	public static function middleware($name) {
		self::$groupStaticProps['middleware'] = (is_array($name) ? $name : [$name]);
		return new self;
	}

	public static function prefix($name) {
		self::$groupStaticProps['prefix'] = $name;
		return new self;
	}

	public static function name($name) {
		self::$groupStaticProps['name'] = $name;
		return new self;
	}
}