<?php
namespace SME\Modules\Cache;

class Object {
	private $store;

	private function getPath($store = null) {
		$store = is_null($store) ? config('cache.default') : $store;
		if (!is_null($this->store))
			$store = $this->store;
		$path = config('cache.stores.'.$store)['path'] ?? null;
		if ($path)
			return $path;
		else
			throw new \Exception('Store '.$store.' not found in config cache', 1);
	}

	private function index() {
		return new Index($this->getPath());
	}

	public function store($name) {
		$this->store = $name;
		return $this;
	}

	public function add($key, $value, $time = 0) {
		if ($this->has($key))
			return false;
		else
			return $this->put($key, $value, $time);
	}

	public function forever($key, $value) {
		return $this->put($key, $value);
	}

	public function put($key, $value, $time = 0) {
		$cache = $this->index()->set($key, $value, $time);
		return file_put_contents($this->getPath().$cache->name, $cache->value) ? true : false;
	}

	public function get($key, $default = "") {
		if ($cache = $this->index()->get($key)) {
			$return = file_get_contents($this->getPath().$cache->name);
			if ($cache->type == 'array' || $cache->type == 'object')
				return unserialize($return);
			return $return;
		}else
			return empty($default) ? NULL : $default;
	}

	public function pull($key) {
		$res = $this->get($key);
		$this->forget($key);
		return $res;
	}
	
	public function forget($key) {
		if ($obj = $this->index()->delete($key))
			return unlink($this->getPath().$obj->name);
	}
	
	public function has($key) {
		return $this->index()->get($key) ? true : false;
	}
	
	public function flush() {
		foreach($this->index()->get() as $cache)
			$this->forget($cache->key);
		return true;
	}
}