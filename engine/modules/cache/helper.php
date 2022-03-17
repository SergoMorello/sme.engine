<?php

use SME\Modules\Cache;

function cache(...$args) {
	$count = count($args);
	if (!$count)
		return new Cache;
	if ($count == 1 && is_string($args[0]))
		return Cache::get($args[0]);
	if ($count >= 1 && $count <=2 && is_array($args[0])) {
		foreach($args[0] as $key => $value) {
			if (!Cache::put($key, $value, intval($args[1] ?? 0)))
				return false;
		}
		return true;
	}
}