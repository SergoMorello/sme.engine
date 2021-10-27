<?php

class http extends httpInc {

	public static function asForm() {
		self::$props['asForm'] = true;
		return new self;
	}
	
	public static function asMultipart() {
		self::$props['asMultipart'] = true;
		return new self;
	}
	
	public static function withBody($body,$contentType=NULL) {
		self::$props['withBody'] = [
			'body'=>$body,
			'contentType'=>$contentType
		];
		return new self;
	}
	
	public static function withBasicAuth($login, $password) {
		self::$props['withBasicAuth'] = [
			'header'=>base64_encode($login.':'.$password)
		];
		return new self;
	}
	
	public static function withDigestAuth($login, $password) {
		self::$props['withDigestAuth'] = [
			'user'=>$login,
			'password'=>$password
		];
		return new self;
	}
	
	public static function withRealm($realm) {
		self::$props['withRealm'] = $realm;
		return new self;
	}
	
	public static function timeout($timeout) {
		self::$props['timeout'] = $timeout;
		return new self;
	}
	
	public static function get($url,$props=[]) {
		return self::query($url,'GET',$props);
	}
	
	public static function post($url,$props=[]) {
		return self::query($url,'POST',$props);
	}
}