<?php

function config($param='') {
	if ($param)
		return config::get($param);
	else
		return new class($param) {
			public function __construct() {
				if (count(core::$arrConfig))
					foreach(core::$arrConfig as $key=>$value)
						$this->$key = $value;
			}
		};
}