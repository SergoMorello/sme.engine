<?php
namespace SME\Core\Request;

class ValidateIs {

	public static function string($var) {
		if (empty($var))
			return true;
		return is_string($var) ? true : false;
	}

	public static function numeric($var) {
		if (empty($var))
			return true;
		return is_numeric($var) ? true : false;
	}

	public static function required($var) {
		return (!empty($var)) ? true : false;
	}

	public static function file($var) {
		if (empty($var))
			return true;
		return ($var instanceof \SME\Core\Request\Objects\Files) ? true : false;
	}

	public static function mimes($var, ...$ext) {
		if (empty($var))
			return true;
		if ($var instanceof \SME\Core\Request\Objects\Files) {
			array_pop($ext);
			if ($var->count()) {
				return array_map(function($file) use (&$ext){
					return in_array($file->getExtension(), $ext);
				},$var);
			}else{
				return in_array($var->getExtension(), $ext);
			}
		}
		return true;
	}

	public static function base64($var) {
		if (empty($var))
			return true;
		return (base64_encode(base64_decode($var, true)) == $var) ? true : false;
	}

	public static function json($var) {
		if (empty($var))
			return true;
		if (is_string($var)) {
            @json_decode($var);
            return (json_last_error() === 0);
        }
		return false;
	}

	public static function regex($var, $pattern) {
		if (empty($var))
			return true;
		return preg_match($pattern, $var) ? true : false;
	}

	public static function not_regex($var, $pattern) {
		if (empty($var))
			return true;
		return (!$this->regex($var, $pattern)) ? true : false;
	}

	public static function max($var, $value) {
		if (empty($var))
			return true;
		return $var <= $value ? true : false;
	}

	public static function min($var, $value) {
		if (empty($var))
			return true;
		return $var >= $value ? true : false;
	}

	public static function size($var, $value) {
		if (empty($var))
			return true;
		if (is_string($var))
			return strlen($var) == $value ? true : false;
		if (is_array($var))
			return count($var) == $value ? true : false;
	}

	public static function unique($var, $model, $column) {
		if (empty($var))
			return true;
		return \SME\Core\controllerCore::model($model)->where($column, $var)->count() ? false : true;
	}

	public static function email($var) {
		if (empty($var))
			return true;
		return filter_var($var, FILTER_VALIDATE_EMAIL) ? true : false;
	}

	public static function ip($var) {
		if (empty($var))
			return true;
		return filter_var($var, FILTER_VALIDATE_IP) ? true : false;
	}

	public static function url($var) {
		if (empty($var))
			return true;
		return filter_var($var, FILTER_VALIDATE_URL) ? true : false;
	}

	public static function same($var, $field) {
		if (empty($var))
			return true;
		return Request::input($field) == $var ? true : false;
	}

	public static function different($var, $field) {
		if (empty($var))
			return true;
		return Request::input($field) != $var ? true : false;
	}

	public static function array($var) {
		if (empty($var))
			return true;
		return is_array($var) ? true : false;
	}
}