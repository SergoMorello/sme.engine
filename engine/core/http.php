<?php

class http extends core {
	static $props=[],$response,$static=[];
	
	
	private static function parseHeaders($headers) {
		if (!is_array($headers))
			return;
		$head = array();
		foreach( $headers as $k=>$v )
		{
			$t = explode( ':', $v, 2 );	
			if( isset( $t[1] ) )
				$head[ trim($t[0]) ] = trim( $t[1] );
			else
			{
				$head[] = $v;
				if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
					$head['reponse_code'] = intval($out[1]);
			}
		}
		return $head;
	}
	
	private static function query($url,$method='GET',$props=[]) {
		$method = strtoupper($method);
		if (!($method=='GET' || $method=='POST'))
			return;
		
		$http = [];
		$http['ignore_errors'] = true;
		$http['method'] = $method;
		$http['header'] = 'Content-Type: application/json';
		$http['content'] = json_encode($props);
		
		
		if (isset(self::$props['asForm']) && self::$props['asForm']==true) {
			$http['header'] = "Content-Type: application/x-www-form-urlencoded";
			$http['content'] = http_build_query($props);
		}
		
		if (isset(self::$props['withBody'])) {
			$http['header'] = self::$props['withBody']['contentType'];
			$http['content'] = self::$props['withBody']['body'];
		}
		
		if (isset(self::$props['withBasicAuth'])) {
			$http['header'] .= PHP_EOL.'Authorization: Basic '.self::$props['withBasicAuth']['header'].PHP_EOL;
		}
		
		if (isset(self::$props['timeout'])) {
			$http['timeout'] = self::$props['timeout'];
		}
		
		if (isset(self::$props['withDigestAuth'])) {
			
			if (!isset(self::$props['DigestAuthLock']) && !isset(self::$props['withRealm'])) {
				self::$props['DigestAuthLock'] = true;
				self::$props['exception'] = false;
				self::$static['backProps'] = self::$props;
				$res = self::query($url,$method,$props);
				self::$props['WWW_Authenticate_header'] = $res->header('WWW-Authenticate');
				self::$props['WWW_Authenticate_body'] = $res->body();
				unset(self::$props['DigestAuthLock']);
			}
			
			if (isset(self::$props['WWW_Authenticate_header']) || isset(self::$props['withRealm'])) {
				
				if (isset(self::$props['WWW_Authenticate_header']))
					parse_str(str_replace(['"',','],[null,'&'],explode(' ',self::$props['WWW_Authenticate_header'])[1]),$authHeaders);
				
				
				$realm = self::$props['withRealm'] ?? ($authHeaders['realm'] ?? '');
				
				$rand = md5(time());
				
				$user = isset(self::$props['withRealm']) ? (self::$props['withDigestAuth']['user'] ?? '') : (self::$static['backProps']['withDigestAuth']['user'] ?? '');
				
				$password = isset(self::$props['withRealm']) ? (self::$props['withDigestAuth']['password'] ?? '') : (self::$static['backProps']['withDigestAuth']['password'] ?? '');
				
				$urlPath = parse_url($url, PHP_URL_PATH);
				
				$nonce = $rand;
				
				$qop = 'sme_auth';
				
				$nc = 1;
				
				$opaque = $rand;
				
				$cnonce = time();
				
				$A1 = md5($user.':'.$realm.':'.$password);
				$A2 = md5($method.':'.$urlPath);
				
				$responseKey = md5($A1.':'.$nonce.':'.$nc.':'.$cnonce.':'.$qop.':'.$A2);
				
				$http['header'] .= PHP_EOL.'Authorization: Digest realm="'.$realm.'", username="'.$user.'", uri="'.$urlPath.'", nonce="'.$nonce.'", opaque="'.$opaque.'", qop="'.$qop.'", nc="'.$nc.'", cnonce="'.$cnonce.'"  response="'.$responseKey.'"'.PHP_EOL;
			}
			
		}
		
		$context = stream_context_create([
			'ssl'=>[
				'verify_peer'=>false,
				'verify_peer_name'=>false,
			],
			'http'=>$http
		]);
		if ($method=='GET' && count($props))
			$url = $url.'/?'.http_build_query($props);
		
		
		$response = new class($url){
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
			
			public function json() {
				if (explode(';',$this->header('Content-Type'))[0]=='application/json')
					return json_decode($this->body(),true);
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
		};
		
		$response->_setBody(@file_get_contents($url, false, $context));
		$response->_setErrors(error_get_last());
		if (isset($http_response_header))
			$response->_setHeaders(self::parseHeaders($http_response_header));
		
		self::$props = [];
		
		return $response;
	}
	
	public static function asForm() {
		self::$props['asForm'] = true;
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