<?php

class httpResponse {
	
	private $_url, $_body, $_headers, $_errors;
	
	public function __construct($url='') {
		$this->_url = $url;
	}
	
	public function _setBody($body) {
		$this->_body = $body;
	}
	
	public function _setHeaders($headers) {
		$this->_headers = $headers;
	}
	
	public function _setErrors($errors) {
		if (isset($errors['message']))
			$errors['message'] = mb_convert_encoding($errors['message'], 'UTF-8');
		$this->_errors = $errors;
	}
	
	public function header($name) {
		if (isset($this->_headers[$name]))
			return $this->_headers[$name];
	}
	
	public function headers() {
		if (is_array($this->_headers))
			return $this->_headers;
	}
	
	public function body() {
		return $this->_body;
	}
	
	public function error() {
		return $this->_errors;
	}
	
	public function json($array=true) {
		if (explode(';',$this->header('Content-Type'))[0]=='application/json')
			return json_decode($this->body(), $array);
		return $this->body();
	}
	
	public function ok() {
		return isset($this->error()['code']) ? false : true;
	}
	
	public function successful() {
		$code = intval($this->header('reponse_code'));
		return ($code>=200 && $code<300) ? true : false;
	}
	
	public function failed() {
		$code = intval($this->header('reponse_code'));
		return ($code>=400 && $code<500) ? true : false;
	}
	
	public function clientError() {
		$code = intval($this->header('reponse_code'));
		return ($code==400) ? true : false;
	}
	
	public function serverError() {
		$code = intval($this->header('reponse_code'));
		return ($code==500) ? true : false;
	}
	
	public function throw($callback=null) {
		if ($this->ok()) {
			$code = intval($this->header('reponse_code'));
			if ($code>=400 && $code<=500)
				$this->_setErrors(['message'=>$this->body()]);
		}
		if (count($this->error())>0) {
			if (is_null($callback))
				exceptions::throw('httpError',[
				'message'=>'HTTP Client: '.explode("):",$this->error()['message'])[1],
				'lines'=>['URL: '.$this->_url,'Response: '.$this->body()]]);
				
			if (is_callable($callback))
				$callback($this, $this->error());
		}
		return $this;
	}
}