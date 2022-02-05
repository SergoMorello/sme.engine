<?php

class DB {

	public static function raw($value) {
		return new class($value) {
			private $value;

			public function __construct($value) {
				$this->value = $value;
			}

			public function getValue() {
				return $this->value;
			}
		};
	}
}