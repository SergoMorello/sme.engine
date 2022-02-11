<?php

class validateIs {

	public function string($var) {
		return is_string($var) ? true : false;
	}

	public function numeric($var) {
		return is_numeric($var) ? true : false;
	}

	public function required($var) {
		return (!empty($var)) ? true : false;
	}

	public function file($var) {
		return (isset($var->tmp_name) && !empty($var->tmp_name)) ? true : false;
	}

	public function base64($var) {
		return (base64_encode(base64_decode($var, true)) == $var) ? true : false;
	}

	public function json($var) {
		if (is_string($var)) {
            @json_decode($var);
            return (json_last_error() === 0);
        }
		return false;
	}

	public function regex($var, $pattern) {
		return preg_match($pattern, $var) ? true : false;
	}

	public function not_regex($var, $pattern) {
		return (!$this->regex($var, $pattern)) ? true : false;
	}
}