<?php
namespace SME\Modules\Cache;

class Index {
	private $cachePath;

	function __construct($cachePath) {
		$this->cachePath = $cachePath;
		$this->check();
	}

	private function check() {
		if (!is_dir($this->cachePath))
			mkdir($this->cachePath, 0777, true);
		if (!file_exists($this->cachePath.'.index'))
			$this->update();
		
		foreach($this->get() as $line)
			if ($line->time > 0 && time() > $line->time)
				if ($obj = $this->delete($line->key))
					@unlink($this->cachePath.$obj->name);
	}

	private function findKey($obj, $key) {
		foreach($obj as $keyIt => $line)
			if ($line->key == $key)
				return $keyIt;
		return -1;
	}

	private function update($obj = '') {
		file_put_contents($this->cachePath.'.index',(empty($obj) ? '[]' : json_encode($obj)));
	}

	private function remArrKey($arr, $key) {
		$res = [];
		foreach($arr as $keyIt => $value) {
			if ($key == $keyIt)
				continue;
			$res[] = $value;
		}
		return $res;
	}

	public function get($key = '') {
		$res = json_decode(file_get_contents($this->cachePath.'.index'));
		if (empty($key))
			return $res;
		foreach($res as $line)
			if ($line->key == $key)
				return $line;
	}

	private function dataType($value) {
		$ret = (object)[
					'type' => 'string',
					'value' => $value
				];
		if (is_callable($value)) {
			$ret->type = 'callable';
			$ret->value = $value();
		}else
		if (is_array($value)) {
			$ret->type = 'array';
			$ret->value = serialize($value);
		}else
		if (is_object($value)) {
			$ret->type = 'object';
			$ret->value = serialize($value);
		}
		return $ret;
	}

	public function set($key, $value, $time) {
		$obj = $this->get();
		$time = $time > 0 ? time() + $time : 0;
		$name = md5($key);
		$valType = $this->dataType($value);
		if (($keyIt = $this->findKey($obj,$key)) >= 0) {
			$objIt = $obj[$keyIt];
			$objIt->time = $time;
			$objIt->type = $valType->type;
		}else
			$obj[] = (object)[
				'key' => $key,
				'time' => $time,
				'type' => $valType->type,
				'name' => $name
			];
		$this->update($obj);
		return (object)[
				'name' => $name,
				'value' => $valType->value
				];
	}

	public function delete($key) {
		$obj = $this->get();
		if (($keyIt = $this->findKey($obj, $key)) >= 0) {
			$deleteObj = $obj[$keyIt];
			$obj = $this->remArrKey($obj, $keyIt);
			$this->update($obj);
			return $deleteObj;
		}
	}
}