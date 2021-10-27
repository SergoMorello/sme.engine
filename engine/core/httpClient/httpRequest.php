<?php

class httpRequest extends httpInc {
	
	private $url, $method, $localProps, $globalProps, $globalStatic;
	
	public function __construct(
						$url,
						$method='GET',
						$props=[]
					) {
		$this->url = $url;
		$this->method = $method;
		$this->localProps = $props;
	}
	
	public function asForm($http) {
		$http['header'] = "Content-Type: application/x-www-form-urlencoded";
		$http['content'] = http_build_query($this->localProps);
		
		return $http;
	}
	
	public function asMultipart($http) {
		$boundary = "--------------------------".time();
			
		$http['header'] = "Content-Type: multipart/form-data; boundary=".$boundary;
		
		$http['content'] = '';
		
		$nl = PHP_EOL;
		
		if (count($this->localProps)>0)
			foreach($this->localProps as $key=>$value) {
				$http['content'] .= '--'.$boundary.$nl;
				$http['content'] .= 'Content-Disposition: form-data; name="'.$key.'"'.$nl.$nl;
				$http['content'] .= $value.$nl.$nl;
			}
		$http['content'] .= '--'.$boundary.'--';
		
		return $http;
	}
	
	public function withBody($http) {
		$http['header'] = "Content-Type: ".self::$props['withBody']['contentType'];
		$http['content'] = self::$props['withBody']['body'];
		
		return $http;
	}
	
	public function withBasicAuth($http) {
		$http['header'] .= PHP_EOL.'Authorization: Basic '.self::$props['withBasicAuth']['header'].PHP_EOL;
		
		return $http;
	}
	
	public function withDigestAuth($http) {
		if (!isset(self::$props['DigestAuthLock']) && !isset(self::$props['withRealm'])) {
				self::$props['DigestAuthLock'] = true;
				self::$props['exception'] = false;
				self::$static['backProps'] = self::$props;
				$res = self::query($this->url,$this->method,$this->localProps);
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
				
				$urlPath = parse_url($this->url, PHP_URL_PATH);
				
				$nonce = $rand;
				
				$qop = 'sme_auth';
				
				$nc = 1;
				
				$opaque = $rand;
				
				$cnonce = time();
				
				$A1 = md5($user.':'.$realm.':'.$password);
				$A2 = md5($this->method.':'.$urlPath);
				
				$responseKey = md5($A1.':'.$nonce.':'.$nc.':'.$cnonce.':'.$qop.':'.$A2);
				
				$http['header'] .= PHP_EOL.'Authorization: Digest realm="'.$realm.'", username="'.$user.'", uri="'.$urlPath.'", nonce="'.$nonce.'", opaque="'.$opaque.'", qop="'.$qop.'", nc="'.$nc.'", cnonce="'.$cnonce.'"  response="'.$responseKey.'"'.PHP_EOL;
			}
		
		return $http;
	}
	
	public function timeout($http) {
		$http['timeout'] = self::$props['timeout'];
		
		return $http;
	}
}