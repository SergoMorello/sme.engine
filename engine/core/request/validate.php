<?php
namespace SME\Core\Request;

class Validate {

	private static function params($value, $field) {
		$params = is_string($value) ? preg_split('/((?<!\\\),)/', $value) : [];
		$params = is_array($value) ? $value : $params;
		$params[] = $field;

		return array_map('trim', $params);
	}

	public static function checkVar($field, $var, $validate) {
		$types = new ValidateIs;
		$return = [];
		$validate = is_array($validate) ? $validate : preg_split('/((?<!\\\)\|)/', $validate);
		foreach($validate as $vl) {
			$vlArr = is_array($vl) ? $vl : preg_split('/((?<!\\\):)/', $vl);
			$type = $vlArr[0];
			$params = self::params($vlArr[1] ?? null, $field);
			
			if (method_exists($types, $type))
				if (!$types->$type($var, ...$params))
					$return[] = $type.(count($params) ? ' '.implode(',',$params) : '');
		}
		return $return;
	}
}