<?php
class appHelper {
	public $config;
	function __construct() {
		$this->config = (object)core::$arrConfig;
	}
	public function config($param) {
		if (isset(core::$arrConfig[$param]))
			return core::$arrConfig[$param];
	}
}

function app() {
	return new appHelper;
}