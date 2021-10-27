<?php
class route extends routeInc {
	
	private function __construct($params) {
		$this->setRoute($params);
	}
	
	public function __destruct() {
		$this->saveRoute();
	}
	
	public static function get($url, $callback) {
		return new self([
			"url"=>$url,
			"callback"=>$callback,
			"method"=>"get"
			]);
	}
	
	public static function post($url, $callback) {
		return new self([
			"url"=>$url,
			"callback"=>$callback,
			"method"=>"post"
			]);
	}
	
	public static function put($url, $callback) {
		return new self([
			"url"=>$url,
			"callback"=>$callback,
			"method"=>"put"
			]);
	}
	
	public static function delete($url, $callback) {
		return new self([
			"url"=>$url,
			"callback"=>$callback,
			"method"=>"delete"
			]);
	}
}