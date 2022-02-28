<?php
namespace SME\Core\Request;

class validateIs {

	public function string($var) {
		if (empty($var))
			return true;
		return is_string($var) ? true : false;
	}

	public function numeric($var) {
		if (empty($var))
			return true;
		return is_numeric($var) ? true : false;
	}

	public function required($var) {
		return (!empty($var)) ? true : false;
	}

	public function file($var) {
		if (empty($var))
			return true;
		return (isset($var->tmp_name) && !empty($var->tmp_name)) ? true : false;
	}

	public function base64($var) {
		if (empty($var))
			return true;
		return (base64_encode(base64_decode($var, true)) == $var) ? true : false;
	}

	public function json($var) {
		if (empty($var))
			return true;
		if (is_string($var)) {
            @json_decode($var);
            return (json_last_error() === 0);
        }
		return false;
	}

	public function regex($var, $pattern) {
		if (empty($var))
			return true;
		return preg_match($pattern, $var) ? true : false;
	}

	public function not_regex($var, $pattern) {
		if (empty($var))
			return true;
		return (!$this->regex($var, $pattern)) ? true : false;
	}

	public function max($var, $value) {
		if (empty($var))
			return true;
		return $var <= $value ? true : false;
	}

	public function min($var, $value) {
		if (empty($var))
			return true;
		return $var >= $value ? true : false;
	}

	public function size($var, $value) {
		if (empty($var))
			return true;
		if (is_string($var))
			return strlen($var) == $value ? true : false;
		if (is_array($var))
			return count($var) == $value ? true : false;
	}

	public function unique($var, $model, $column) {
		if (empty($var))
			return true;
		return controller::model($model)->where($column, $var)->count() ? false : true;
	}

	public function email($var) {
		if (empty($var))
			return true;
		return filter_var($var, FILTER_VALIDATE_EMAIL) ? true : false;
	}

	public function ip($var) {
		if (empty($var))
			return true;
		return filter_var($var, FILTER_VALIDATE_IP) ? true : false;
	}

	public function url($var) {
		if (empty($var))
			return true;
		return filter_var($var, FILTER_VALIDATE_URL) ? true : false;
	}

	public function same($var, $field) {
		if (empty($var))
			return true;
		return request::input($field) == $var ? true : false;
	}

	public function different($var, $field) {
		if (empty($var))
			return true;
		return request::input($field) != $var ? true : false;
	}

	public function array($var) {
		if (empty($var))
			return true;
		return is_array($var) ? true : false;
	}
}