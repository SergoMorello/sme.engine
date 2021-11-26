<?php
class session {
	public function delete($data) {
		if (is_array($data))
			foreach($data as $dt)
				unset($_SESSION[$dt]);
		elseif (is_string($data))
			unset($_SESSION[$data]);
	}
}
function session($data=null) {
	if (is_null($data))
		return new session;
	if (is_array($data)) {
		foreach($data as $key=>$val)
			$_SESSION[$key] = $val;
	}
	if (is_string($data))
		return $_SESSION[$data] ?? NULL;
}