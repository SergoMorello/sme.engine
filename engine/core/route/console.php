<?php
use SME\Core\Route\RouteCore;

class console extends RouteCore {
	
	private function __construct($params) {
		$this->setRoute($params);
	}
	
	public function __destruct() {
		$this->saveRoute();
	}
	
	public static function command($url, $callback) {
		return new self([
			"url"=>$url,
			"callback"=>$callback,
			"method"=>"command"
			]);
	}
}