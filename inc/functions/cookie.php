<?php
class cookie {
	public function delete($data) {
		return setcookie($data, NULL);
	}
}
function cookie($data=[],$time=0) {
	if (!count($data))
		return new cookie;
	if (is_array($data)) {
		foreach($data as $key=>$val) {
			$arrData = is_array($val) ? $val : array("value"=>$val,"date"=>($time ? time()+$time : time()+(3600*24*30)));
			return setcookie($key, $arrData['value'], $arrData['date'], "/");
		}
	}else
		return $_COOKIE[$data] ?? NULL;
}