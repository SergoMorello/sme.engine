<?php
namespace SME\Core\Route;

class RouteWeb extends RouteCore {
	public function get($url, $callback) {
		return $this->setRoute([
			"url" => $url,
			"callback" => $callback,
			"method" => "get"
			]);
	}
	
	public function post($url, $callback) {
		return $this->setRoute([
			"url" => $url,
			"callback" => $callback,
			"method" => "post"
			]);
	}
	
	public function put($url, $callback) {
		return $this->setRoute([
			"url" => $url,
			"callback" => $callback,
			"method" => "put"
			]);
	}
	
	public function delete($url, $callback) {
		return $this->setRoute([
			"url" => $url,
			"callback" => $callback,
			"method" => "delete"
			]);
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
	
	public function prefix($name) {
		$this->route['prefix'] = $name;
		return $this;
	}
}