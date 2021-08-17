<?php
class route extends core {
	private static function setRoute($params) {
		$params['callback'] = is_string($params['callback']) ? explode("@",$params['callback']) : $params['callback'];
		array_push(core::$pagesArr,$params);
	}
	public static function get($url,$callback,$params=array()) {
		self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"get","params"=>$params));
	}
	public static function post($url,$callback,$params=array()) {
		self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"post","params"=>$params));
	}
	public static function put($url,$callback,$params=array()) {
		self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"put","params"=>$params));
	}
	public static function delete($url,$callback,$params=array()) {
		self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"delete","params"=>$params));
	}
}