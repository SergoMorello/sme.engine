<?php
namespace SME\Core\Request;

class Session {
	private $__session;

	public function __construct($session) {
		$this->__session = $session;
	}

	public function __destruct() {
		$_SESSION = $this->__session;
	}

	public function put($key, $value = null) {
		if (is_string($key) && !is_null($value))
			$this->__session[$key] = $value;
		if (is_array($key) && is_null($value)) {
			foreach($key as $k => $v)
				$this->__session[$k] = $v;
		}
	}

	public function push($key, $pushValue) {
		if ($this->exists($key)) {
			$value = $this->get($key);
			if (is_array($value))
				$value[] = $pushValue;
			if (is_string($value))
				$value .= $pushValue;
			$this->__session[$key] = $value;
		}
	}

	public function pull($key, $default = null) {
		$value = $this->get($key, $default);
		$this->forget($key);
		return $value;
	}

	public function forget($key) {
		if (is_string($key))
			unset($this->__session[$key]);
		if (is_array($key)) {
			foreach($key as $k) {
				unset($this->__session[$k]);
			}
		}
		return true;
	}

	public function flush() {
		unset($this->__session);
		return true;
	}

	public function get($key, $default = null) {
		if (isset($this->__session[$key])) {
			return $this->__session[$key];
		}else{
			if (is_string($default))
				return $default;
			if (is_callable($default))
				return $default();
		}
	}

	public function all() {
		return $this->__session;
	}

	public function has($key) {
		return ($this->exists($key) && !is_null($this->__session[$key]));
	}

	public function exists($key) {
		return isset($this->__session[$key]);
	}

	public function missing($key) {
		return !isset($this->__session[$key]);
	}
}