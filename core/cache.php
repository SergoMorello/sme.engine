<?php

class cache extends core {
	public static function put($key,$value,$time=0) {
		$name = self::index()->set($key,$time);
		file_put_contents(self::dirCache.$name,(is_callable($value) ? $value() : $value));
	}
	private static function index() {
		return (new class{
			function __construct() {
				$this->check();
			}
			private function check() {
				if (!file_exists(core::dirCache.'.index'))
					$this->update();
			}
			private function findKey($obj,$key) {
				foreach($obj as $keyIt=>$line)
					if ($line->key==$key)
						return $keyIt;
				return -1;
			}
			private function update($obj="") {
				file_put_contents(core::dirCache.'.index',(empty($obj) ? '[]' : json_encode($obj)));
			}
			private function remArrKey($arr,$key) {
				$res = [];
				foreach($arr as $keyIt=>$value) {
					if ($key==$keyIt)
						continue;
					$res[] = $value;
				}
				return $res;
			}
			public function get($key="") {
				$res = json_decode(file_get_contents(core::dirCache.'.index'));
				if (empty($key))
					return $res;
				foreach($res as $line)
					if ($line->key==$key)
						return $line;
			}
			public function set($key,$time) {
				$obj = $this->get();
				
				$name = md5($key);
				if (($keyIt = $this->findKey($obj,$key))>=0)
					$obj[$keyIt]->time = $time;
				else
					$obj[] = (object)['key'=>$key,'time'=>$time,'name'=>$name];
				$this->update($obj);
				return $name;
			}
			public function delete($key) {
				$obj = $this->get();
				if (($keyIt = $this->findKey($obj,$key))>=0) {
					$deleteObj = $obj[$keyIt];
					$obj = $this->remArrKey($obj,$keyIt);
					$this->update($obj);
					return $deleteObj;
				}
			}
		});
	}
	public static function get($key) {
		if ($cache = self::index()->get($key))
			return file_get_contents(self::dirCache.$cache->name);
	}
	public static function pull($key) {
		$res = self::get($key);
		self::forget($key);
		return $res;
	}
	public static function forget($key) {
		if ($obj = self::index()->delete($key))
			return unlink(self::dirCache.$obj->name);
	}
	public static function has($key) {
		return self::index()->get($key) ? true : false;
	}
}