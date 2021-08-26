<?php
class session {
	public function delete($data) {
		unset($_SESSION[$data]);
	}
}
function session($data=[]) {
	if (!count($data))
		return new session;
	if (is_array($data)) {
		foreach($data as $key=>$val) {
			$_SESSION[$key] = $val;
		}
	}else
		return $_SESSION[$data] ?? NULL;
}