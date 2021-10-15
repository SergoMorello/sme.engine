<?php
class response {
	private static function setCode($code) {
		if (isset($_SERVER['SERVER_PROTOCOL']))
			header($_SERVER['SERVER_PROTOCOL']." ".$code);
	}
	public static function json($arr=[],$code=200) {
		$arr = (array)$arr;	
		header('Content-Type: application/json');
		self::setCode($code);
		return json_encode($arr,true);
	}
}
function response($data=NULL,$code=200) {
	return is_null($data) ? new response($data,$code) : $data;
}