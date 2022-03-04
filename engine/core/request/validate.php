<?php
namespace SME\Core\Request;


class Validate {
	private static $__rules = [];

	private static function params($value, $field) {
		$params = is_string($value) ? preg_split('/((?<!\\\),)/', $value) : [];
		$params = is_array($value) ? $value : $params;
		$params[] = $field;

		return array_map('trim', $params);
	}

	public static function checkVar($field, $var, $validate) {
		$return = [];
		$validate = is_array($validate) ? $validate : preg_split('/((?<!\\\)\|)/', $validate);
		foreach($validate as $vl) {
			$vlArr = is_array($vl) ? $vl : preg_split('/((?<!\\\):)/', $vl);
			$type = $vlArr[0] ?? '';
			$params = self::params($vlArr[1] ?? null, $field);

			if ($result = self::validateMethod($type, $var, $params))
				$return[] = $result;
		}
		return $return;
	}

	private static function validateMethod($method, $var, $params) {
		foreach(self::$__rules as $rule) {
			if ($method == $rule['name'])
				if (!$rule['callback']($var, ...$params))
					return $method.(count($params) ? ' '.implode(',',$params) : '');
		}
		if (method_exists('\\SME\\Core\\Request\\ValidateIs', $method))
			if (!ValidateIs::$method($var, ...$params))
				return [
					'method' => $method,
					'field' => array_pop($params),
					'params' => $params
				];
	}

	public static function rule($name, $callback) {
		self::$__rules[] = [
			'name' => $name,
			'callback' => $callback
		];
	}
}