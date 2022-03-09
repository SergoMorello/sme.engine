<?php
namespace SME\Core;

class Lang {

	public static function get($string, $params = []) {
		$obj = self::getData($string);
		$tempGet = $obj->data;
		if (!count($obj->path))
			return $string;
		foreach($obj->path as $key) {
			if (isset($tempGet[$key]))
				$tempGet = $tempGet[$key];
			else
				return $string;
		}
		if (is_array($tempGet))
			return $string;
		if (isset($obj->data['attributes']) && is_array($obj->data['attributes']))
			return self::setParams($tempGet, $params, $obj->data['attributes']);
		return self::setParams($tempGet, $params);
	}

	public static function has($string) {
		return $string != self::get($string) ? true : false;
	}

	private static function getData($string) {
		$pathArr = explode('.', $string);
		$data = App::include('app.Lang.'.App::getLocale().'.'.$pathArr[0]);
		unset($pathArr[0]);
		return (object)[
			'path' => $pathArr,
			'data' => $data
		];
	}

	private static function setParams($string, $params, $replace = []) {
		foreach($params as $key => $value) {
			if (isset($replace[$value]))
				$value = $replace[$value];
			$string = str_ireplace(':'.$key, $value, $string);
		}
		return $string;
	}
}