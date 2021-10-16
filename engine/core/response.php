<?php
class response {
	public static function сode($code) {
		if (isset($_SERVER['SERVER_PROTOCOL']))
			return self::header($_SERVER['SERVER_PROTOCOL'].' '.$code);
	}
	
	public static function header(...$header) {
		if (is_array($header)) {
			if (count($header)==2)
				header($header[0].': '.$header[1]);
			if (count($header)==1)
				header($header[0]);
		}
		return new self;
	}
	
	public static function json($arr=[],$code=200) {
		$arr = (array)$arr;	
		self::header('Content-Type', 'application/json')->сode($code);
		return json_encode($arr,true);
	}
}
