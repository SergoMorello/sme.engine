<?php
namespace SME\Core\View;

class Errors {
	private static $fields = [];

	public function __construct() {
		if ($errors = session('__withErrors')) {
			foreach($errors as $error => $access)
				$this->{$error} = $access;
		}
	}

	private function errors() {
		return get_object_vars($this);
	}
	
	public function has($name) {
		return empty($this->first($name)) ? false : true;
	}

	public function count() {
		return count($this->errors());
	}

	public function first($name) {
		if (!count($this->errors()))
			return '';
		if (isset(self::$fields[$name]))
			return self::$fields[$name];
		foreach($this->errors() as $error) {
			if ($error['field'] == $name)
				return self::$fields[$name] = $error['message'];
		}
		return '';
	}

	public function any() {
		return count($this->errors()) ? true : false;
	}

	public function all() {
		return $this->errors();
	}
}