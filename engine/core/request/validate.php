<?php
namespace SME\Core\Request;

class Validate {

	public static function checkVar($field, $var, $validate) {
		$types = new ValidateIs;
		$return = [];
		$validate = is_array($validate) ? $validate : explode('|',$validate);
		foreach($validate as $vl) {
			$vlArr = explode(':', $vl);
			$type = $vlArr[0];
			$params = isset($vlArr[1]) ? explode(',', $vlArr[1]) : [];
			$params[] = $field;

			$params = array_map('trim', $params);

			if (method_exists($types, $type))
				if (!$types->$type($var, ...$params))
					$return[] = $type.(count($params) ? ' '.implode(',',$params) : '');
		}
		return $return;
	}
}