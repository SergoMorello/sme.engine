<?php
namespace SME\Core\Request;

use SME\Core\Core;
use SME\Core\Exception;
use SME\Modules\storage;

use SME\Core\Request\Objects\Files;
use SME\Core\Request\Objects\Session;
use SME\Core\Request\Objects\Cookie;

class Request extends Core {
	private static $_server, $_get, $_post, $_session, $_cookie, $_files, $_headers;
	
	public static function __init() {
		self::$_server = $_SERVER;
		self::$_get = Core::guardData($_GET);
		self::$_post = Core::guardData($_POST);
		self::$_session = new Session($_SESSION);
		self::$_cookie = new Cookie($_COOKIE);
		self::$_files = $_FILES;
		self::$_headers = self::getallheaders();
	}

	public static function route($var) {
		if (is_string($var))
			return \Route::getProps($var);
	}
	
	public static function server($var='') {
		if (!is_string($var))
			return null;
		if (empty($var))
			return (object)self::$_server;
		return self::$_server[$var] ?? null;
	}

	public static function cookie($name = null) {
		if (is_null($name))
			return self::$_cookie;
		return (self::$_cookie)->get($name);
	}

	public static function session() {
		return self::$_session;
	} 

	public static function all() {
		return [
			'post' => self::$_post,
			'get' => self::$_get,
			'route' => \Route::getProps(),
			'files' => self::$_files
		];
	}

	private static function getallheaders() {
		$headers = [];
		foreach (self::$_server as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}

	public static function header($name = null) {
		if (is_null($name)) {
			return (object)self::$_headers;
		}else{
			if (isset(self::$_headers[$name]))
				return self::$_headers[$name];
		}
	}

	private static function getDotArray($string, $data) {
		$splitVars = preg_split('/((?<!\\\)\.)/', $string);

		$findData = function($splitVars, $data) use (&$findData) {
			foreach($splitVars as $key) {
				if (isset($data[$key])) {
					if (is_array($data[$key]) || is_object($data[$key]))
						return $findData($splitVars, $data[$key]);
					else
						return $data[$key];
				}
			}
		};

		return $findData($splitVars, $data);
	}

	public static function input($var) {
		if (is_string($var)) {
			if ($value = self::getDotArray($var, self::$_post))
				return $value;
			
			return self::$_post[$var] ?? self::$_get[$var] ?? (isset($_FILES[$var]) ? self::file($var) : NULL);
		}
	}
	
	public static function file($var) {
		if (!is_string($var) || !isset(self::$_files[$var]) || empty(self::$_files[$var]['tmp_name']) || (isset(self::$_files[$var]['tmp_name'][0]) && empty(self::$_files[$var]['tmp_name'][0])))
			return;
		return new Files(self::$_files[$var]);
	}
	
	public static function hasFile($var) {
		if (isset(self::$_files[$var]))
			return true;
		return false;
	}
	
	public static function has($var) {
		if (isset(self::$_post[$var]))
			return true;
		if (\Route::getProps($var))
			return true;
		if (isset(self::$_get[$var]))
			return true;
		if (self::hasFile($var))
			return true;
		return false;
	}
	
	public static function json() {
		return json_decode(file_get_contents('php://input'));
	}
	
	public static function validate($data, $return = false) {
		if (!is_array($data))
			return;

		$validateErr = [];
		foreach($data as $var => $access) {
			if ($validateResult = Validate::checkVar($var,
				self::file($var) ?? self::input($var) ?? self::route($var)
				,$access))
				$validateErr[] = $validateResult;
			
		}
			
		
		if (count($validateErr)) {
			if ($return)
				return true;
			else{
				$validateErrMessages = [];
				foreach($validateErr as $parentError) {
					foreach($parentError as $error)
						$validateErrMessages[] = [
							'field' => $error['field'],
							'message' => trans('validate.'.$error['method'], ['field' => $error['field'], 'params' => implode(',', $error['params'])])
						];
				}
				throw new \SME\Exceptions\Validate($validateErr, $validateErrMessages);
			}	
		}
	}
}
