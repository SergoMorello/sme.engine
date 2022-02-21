<?php

class validate {

	public static function checkVar($var, $validate) {
		$types = new validateIs;
		$return = [];
		foreach(explode('|',$validate) as $vl) {
			$vlArr = explode(':', $vl);
			$type = $vlArr[0];
			$params = $vlArr[1] ?? '';

			if (method_exists($types, $type))
				if (!$types->$type($var, $params))
					$return[] = $type.($params ? ' '.$params : '');
		}
		return $return;
	}
}