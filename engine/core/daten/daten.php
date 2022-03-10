<?php
namespace SME\Core\Daten;

class Daten {
	public static function now() {
		return new DatenObject(time());
	}
}