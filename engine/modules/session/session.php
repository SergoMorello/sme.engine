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