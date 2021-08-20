<?php
class route extends core {
	function __construct($index=0) {
		parent::__construct();
		$this->lastIndex = $index;
	}
	private static function setRoute($params) {
		$params['callback'] = is_string($params['callback']) ? explode("@",$params['callback']) : $params['callback'];
		$index = array_push(core::$pagesArr,$params)-1;
		return new self($index);
	}
	public static function get($url,$callback,$params=array()) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"get","params"=>$params));
	}
	public static function post($url,$callback,$params=array()) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"post","params"=>$params));
	}
	public static function put($url,$callback,$params=array()) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"put","params"=>$params));
	}
	public static function delete($url,$callback,$params=array()) {
		return self::setRoute(array("url"=>$url,"callback"=>$callback,"method"=>"delete","params"=>$params));
	}
	public function name($name) {
		core::$pagesArr[$this->lastIndex]['name'] = $name;
	}
}