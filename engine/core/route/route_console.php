<?php
namespace SME\Core\Route;

class RouteConsole extends RouteCore {

	public function command($url, $callback) {
		return $this->setRoute([
			"url" => $url,
			"callback" => $callback,
			"method" => "command"
			]);
	}
}