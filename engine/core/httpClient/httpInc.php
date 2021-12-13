<?php

abstract class httpInc extends core {
	protected static $props=[], $response, $static=[];
	
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
	
	protected static function query($url,$method='GET',$props=[]) {
		$method = strtoupper($method);
		if (!($method=='GET' || $method=='POST'))
			return;
		
		$http = [];
		$http['ignore_errors'] = true;
		$http['method'] = $method;
		$http['header'] = 'Content-Type: application/json';

		$http['content'] = json_encode($props);

		if (isset($props['headers']) && is_array($props['headers']) && isset($props['body'])) {
			$http['header'] .= PHP_EOL;
			foreach($props['headers'] as $key=>$value)
				$http['header'] .= $key.': '.$value.PHP_EOL;
			$props = $props['body'];
			$http['content'] = $props;
		}
		
		$request = new httpRequest(
				$url,
				$method,
				$props
				);
		
		if (isset(self::$props['asForm'])) {
			$http = $request->asForm($http);
		}
		
		if (isset(self::$props['asMultipart'])) {
			$http = $request->asMultipart($http);
		}
		
		if (isset(self::$props['withBody'])) {
			$http = $request->withBody($http);
		}
		
		if (isset(self::$props['withBasicAuth'])) {
			$http = $request->withBasicAuth($http);
		}
		
		if (isset(self::$props['timeout'])) {
			$http = $request->timeout($http);
		}
		
		if (isset(self::$props['withDigestAuth'])) {
			$http = $request->withDigestAuth($http);
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
		
		$response = new httpResponse($url);
		
		$response->_setBody(@file_get_contents($url, false, $context));
		$response->_setErrors(error_get_last());
		if (isset($http_response_header))
			$response->_setHeaders(self::parseHeaders($http_response_header));
		
		self::$props = [];
		
		return $response;
	}
}