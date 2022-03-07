<?php
namespace SME\Core\Route;

class RouteObject extends RouteCore {

	public function __construct($params) {
		$this->setRoute($params);
	}
	
	public function __destruct() {
		$this->saveRoute();
	}

	public function name($name) {
		$this->route['name'] = ($this->route['name'] ?? '').$name;
		return $this;
	}
	
	public function middleware($name) {
		$this->route['middleware'] = (is_array($name) ? $name : [$name]);
		return $this;
	}
	
	public function where($where) {
		$this->route['where'] = $where;
		return $this;
	}
}