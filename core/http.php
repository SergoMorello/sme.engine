<?php

class http extends core {
	static $props=[];
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
		
		$header = "application/json";
		$content = json_encode($props);
		
		if (isset(self::$props['asForm']) && self::$props['asForm']==true) {
			$header = "application/x-www-form-urlencoded";
			$content = http_build_query($props);
		}
		if (isset(self::$props['withBody'])) {
			$header = self::$props['withBody']['contentType'];
			$content = self::$props['withBody']['body'];
		}
		$headers = stream_context_create([
			"ssl"=>[
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			],
			"http"=>[
				"method"=>$method,
				"header"=>"Content-Type: ".$header,
				"content"=>$content
			]
		]);
		if ($method=='GET' && count($props))
			$url = $url.'/?'.http_build_query($props);
		$response = (object)[];
		
		try{
			$response->body = file_get_contents($url, false, $headers);
		}catch (Exception $ex) {
			view::error('error',['message'=>'HTTP Client: '.explode("):",$ex->getMessage())[1]]);
		}
		
		$response->header = self::parseHeaders($http_response_header);
		self::$props = [];
		return $response;
	}
	public static function get($url,$props=[]) {
		$query = self::query($url,'GET',$props);
		if ($query->header['Content-Type']=='application/json')
			return json_decode($query->body,true);
		return $query->body;
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
	public static function post($url,$props=[]) {
		$query = self::query($url,'POST',$props);
		if ($query->header['Content-Type']=='application/json')
			return json_decode($query->body,true);
		return $query->body;
	}
}